<?php

namespace App\Services\B2bslots;

use App\Models\Game;
use App\Models\GameSession;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserBonus;
use App\Services\B2bslots\BonusService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

use App\Services\B2bslots\Actions\CreateGame;

class Client
{
    private $url;
    private $operator_id;


    public function __construct(string $url, int $operator_id)
    {
        $this->url = $url;
        $this->operator_id = $operator_id;
    }

    public function getGames(): array
    {
        $path = "frontendsrv/apihandler.api?cmd=";
        $params = json_encode([
            "api" => "ls-games-by-operator-id-get",
            "operator_id"       => config('services.b2bslots.operator_id'),
        ]);

        $url = $this->url . $path . $params;
        $response = Http::get($url);

        if (!$response->successful()) {
            return $response->toException();
        }

        $responseData = $response->object();
        $groups = [];

        foreach ($responseData->locator as $group) {
            if (is_array($group)) {
                $groups[] = $group;
            }
        }

        if (empty($groups)) {
            throw new \Exception('No groups found');
        }

        $games = [];

        foreach ($groups[0] as $key => $group) {
            foreach ($group->games as $key => $currentGame) {
                $game = [
                    'id' => $currentGame->gm_bk_id,
                    'provider' => $group->gr_title,
                    'title' => $currentGame->gm_title,
                    'images' => $currentGame->icons,
                ];
                array_push(
                    $games,
                    CreateGame::handle($game)
                );
            }
        }

        return $games;
    }

    public function getToken(string $token)
    {
        $session = GameSession::where("session_id", "=", $token)->first();
        if (!$session) {
            return false;
        }

        return $session;
    }

    public function validationData(User $user, Game $game, array $data)
    {
        $id = intval($data['user_id'] ?? null);
        $authToken = $data['user_auth_token'] ?? null;
        $currency = $data['currency'] ?? null;
        $userIp = $data['user_ip'] ?? null;
        $gameCode = $data['game_code'] ?? null;

        if ($user->id !== $id) {
            throw new \InvalidArgumentException(config('enums.b2bslotsErrors.user'));
        }

        if ($user->identity !== $authToken) {
            throw new \InvalidArgumentException(config('enums.b2bslotsErrors.auth'));
        }

        if ($user->currency !== $currency) {
            throw new \InvalidArgumentException(config('enums.b2bslotsErrors.currency'));
        }

        if ($userIp !== realIp()) {
            throw new \InvalidArgumentException(config('enums.b2bslotsErrors.ip'));
        }

        if (intval($game->info) !== $gameCode) {
            throw new \InvalidArgumentException(config('enums.b2bslotsErrors.game'));
        }

        return true;
    }

    public function validateBalance(User $user, $amount)
    {
        if ($amount < 0) {
            return $this->respondWithError(
                config('enums.b2bslotsCodes.BalanceError'),
                "Invalid amount. Amount must be positive."
            );
        }

        if ($user->balance < $amount) {
            return $this->respondWithError(
                config('enums.b2bslotsCodes.BalanceError'),
                "Insufficient balance."
            );
        }

        return true;
    }

    public function activateBonus(User $user, $data)
    {
        $bonusId = $data['free_rounds']['id'];
        $provider = config('enums.game_types.b2bslots');

        UserBonus::firstOrCreate(
            [
                'bonus_id' => $bonusId,
                'user_id' => $user->id,
                'status' => true
            ],
            [
                'bonus_id' => $bonusId,
                'user_id' => $user->id,
                'provider' => $provider,
            ]
        );
    }

    public function updateBonus(User $user, $data)
    {
        $bonusId = $data['free_rounds']['id'];
        $win = $data['free_rounds']['win'];
        $roundWin = $data['free_rounds']['round_win'];
        $count = $data['free_rounds']['count'];
        $played = $data['free_rounds']['played'];

        $userBonus = UserBonus::where([
            'bonus_id' => $bonusId,
            'user_id' => $user->id,
            'status' => true,
        ])->first();

        if ($userBonus) {
            $userBonus->update([
                'win' => $win,
                'round_win' => $roundWin,
                'count' => $count,
                'played' => $played,
            ]);
        }
    }

    public function endBonus(User $user, $data)
    {
        $bonusId = $data['free_rounds']['id'];
        $win = $data['free_rounds']['win'];

        $userBonus = UserBonus::where([
            'bonus_id' => $bonusId,
            'user_id' => $user->id,
            'status' => true,
        ])->first();

        if ($userBonus) {
            $userBonus->update([
                'win' => $win,
                'round_win' => 0,
                'count' => 0,
                'end' => date("Y-m-d H:i:s"),
                'status' => false,
            ]);
            $this->updateBalance($user, $win, 'credit');
        }
    }

    public function createTransactionAndUpdateBalance(User $user, GameSession $session, float $amount, string $transactionHash, int $roundId, string $operationType, $gameCode): void
    {
        if ($operationType === 'debit') {
            $transactionType = config('enums.transaction_types.withdraw');
        } elseif ($operationType === 'credit') {
            $transactionType = config('enums.transaction_types.deposit');
        }

        $gameId = Game::where('info', $gameCode)->value('id');
        
        DB::beginTransaction();
        try {
            Transaction::create([
                'amount' => $amount,
                'currency' => $user->currency,
                'user_id' => $user->id,
                'reference' => $transactionHash,
                'game_session_id' => $session->id,
                'game_round_id' => strval($roundId),
                'game_id' => $gameId,
                'type' => $transactionType,
                'completed' => true
            ]);

            $this->updateBalance($user, $amount, $operationType);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function authResponse(User $user, $demo = false): array
    {
        $gameToken = GameSession::whereNull('end')
            ->where('user_id', '=', $user->id)
            ->first()
            ->value('session_id');

        return [
            "answer" => [
                "operator_id" => $demo ? $this->operator_id : 0,
                "user_id" => $user->id,
                "user_nickname" => "Anonimous",
                "balance" => formatBalance($user->balance),
                "bonus_balance" => formatBalance($user->bonus),
                "auth_token" => $user->identity,
                "game_token" => $gameToken,
                "error_code" => config('enums.b2bslotsCodes.Success'),
                "error_description" => "ok",
                "currency" => $user->currency,
                "timestamp" => time(),
            ],
            "success" => true,
            "api" => "do-auth-user-ingame",
        ];
    }

    public function debitResponse(User $user,  $data)
    {
        return [
            "answer" => [
                "operator_id" => $this->operator_id,
                "transaction_id" => $data['transaction_id'],
                "user_id" => $data['user_id'],
                "user_nickname" => "Anonimous",
                "balance" => formatBalance($user->balance),
                "bonus_balance" => formatBalance($user->bonus),
                "bonus_amount" => "0.0",
                "game_token" => $data['user_game_token'],
                "error_code" => config('enums.b2bslotsCodes.Success'),
                "error_description" => "ok",
                "currency" => $user->currency,
                "timestamp" => time(),
            ],
            "success" => true,
            "api" => "do-debit-user-ingame",
        ];
    }

    public function creditResponse(User $user,  $data)
    {
        return [
            "answer" => [
                "operator_id" => $this->operator_id,
                "transaction_id" => $data['transaction_id'],
                "user_id" => $data['user_id'],
                "user_nickname" => "Anonimous",
                "balance" => formatBalance($user->balance),
                "bonus_balance" => formatBalance($user->bonus),
                "bonus_amount" => "0.0",
                "game_token" => $data['user_game_token'],
                "error_code" => config('enums.b2bslotsCodes.Success'),
                "error_description" => "ok",
                "currency" => $user->currency,
                "timestamp" => time(),
            ],
            "success" => true,
            "api" => "do-credit-user-ingame",
        ];
    }

    public function getFeatureResponse(User $user,  $data)
    {
        return [
            "answer" => [
                "operator_id" => $this->operator_id,
                "user_id" => $data['user_id'],
                "user_nickname" => "Anonimous",
                "balance" => formatBalance($user->balance),
                "bonus_balance" => formatBalance($user->bonus),
                "game_token" => $data['user_game_token'],
                "error_code" => config('enums.b2bslotsCodes.Success'),
                "error_description" => "ok",
                "currency" => $user->currency,
                "timestamp" => time(),
                "free_rounds" => BonusService::getBonuses($user),
            ],
            "success" => true,
            "api" => "do-get-features-user-ingame",
        ];
    }

    public function activateFeatureResponse(User $user,  $data)
    {
        return [
            "answer" => [
                "operator_id" => $this->operator_id,
                "user_id" => $data['user_id'],
                "user_nickname" => "Anonimous",
                "balance" => formatBalance($user->balance),
                "bonus_balance" => formatBalance($user->bonus),
                "game_token" => $data['user_game_token'],
                "error_code" => config('enums.b2bslotsCodes.Success'),
                "error_description" => "ok",
                "currency" => $user->currency,
                "timestamp" => time(),
            ],
            "success" => true,
            "api" => "do-activate-features-user-ingame",
        ];
    }

    public function updateFeatureResponse(User $user,  $data)
    {
        return [
            "answer" => [
                "operator_id" => $this->operator_id,
                "user_id" => $data['user_id'],
                "user_nickname" => "Anonimous",
                "balance" => formatBalance($user->balance),
                "bonus_balance" => formatBalance($user->bonus),
                "game_token" => $data['user_game_token'],
                "error_code" => config('enums.b2bslotsCodes.Success'),
                "error_description" => "ok",
                "currency" => $user->currency,
                "timestamp" => time(),
            ],
            "success" => true,
            "api" => "do-update-features-user-ingame",
        ];
    }

    public function endFeatureResponse(User $user,  $data)
    {
        return [
            "answer" => [
                "operator_id" => $this->operator_id,
                "user_id" => $data['user_id'],
                "user_nickname" => "Anonimous",
                "balance" => formatBalance($user->balance),
                "bonus_balance" => formatBalance($user->bonus),
                "game_token" => $data['user_game_token'],
                "error_code" => config('enums.b2bslotsCodes.Success'),
                "error_description" => "ok",
                "currency" => $user->currency,
                "timestamp" => time(),
            ],
            "success" => true,
            "api" => "do-end-features-user-ingame",
        ];
    }

    public function generateToken(array $params): string
    {
        $dataString = implode(':', $params);
        $randomBytes = openssl_random_pseudo_bytes(64);
        $randomHash = bin2hex($randomBytes);
        $token = Hash::make($dataString . $randomHash);

        return $token;
    }


    public function respondWithError($code, $message)
    {
        return response()->json(["error" => $code, "message" => $message], 400);
    }

    private function updateBalance(User $user, float $amount, string $operationType)
    {
        if ($operationType === 'debit') {
            $user->balance -= $amount;
        } elseif ($operationType === 'credit') {
            $user->balance += $amount;
        }

        $user->save();
    }
}
