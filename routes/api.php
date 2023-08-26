<?php

use App\Http\Controllers\Admin\ClientUpdateBalanceController;
use App\Http\Controllers\Admin\ConfirmWithdrawController;
use App\Http\Controllers\Admin\MetricsController;
use App\Http\Controllers\Admin\WalletInvoiceController;
use App\Http\Controllers\Admin\WalletWithdrawController;
use App\Http\Controllers\AffiliateManagerController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\B2bslots\B2bslotsServiceController;
use App\Http\Controllers\B2bslots\testController as B2bslotsTestController;
use App\Http\Controllers\Cabinet\CloseSessionController;
use App\Http\Controllers\Cabinet\DemoGameController;
use App\Http\Controllers\Cabinet\GamesController;
use App\Http\Controllers\Cabinet\StartGameController;
use App\Http\Controllers\Cabinet\StrikeModeController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\Mancala\MancalaServiceController;
use App\Http\Controllers\Mancala\TestController;
use App\Http\Controllers\Payment\BalanceController;
use App\Http\Controllers\Payment\Status\PaymentStatusController;
use App\Http\Controllers\Payment\Status\WalletInvoiceStatusController;
use App\Http\Controllers\Payment\Status\WalletWithdrawStatusController;
use App\Http\Controllers\Payment\Status\WithdrawStatusController;
use App\Http\Controllers\Payment\UserInvoiceController;
use App\Http\Controllers\Payment\UserWithdrawController;
use App\Http\Controllers\RatesController;
use App\Http\Controllers\ReferralController;
use App\Http\Controllers\TomHorn\WalletServiceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(
    [
        'middleware' => 'api',
        'prefix' => 'auth'
    ],
    function ($router) {
        Route::post('login', [AuthController::class, 'login']);
        Route::post('register', [AuthController::class, 'register']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::post('me', [AuthController::class, 'me']);
        Route::post('google', [AuthController::class, 'googleLogin']);
        Route::post('one_click', [AuthController::class, 'oneClick']);
    }
);

Route::apiResource('clients', ClientController::class)->middleware('auth:api');
Route::get('mancala/test', TestController::class);
Route::get('rates', RatesController::class);
//Route::apiResource('providers', ProviderController::class);
//Route::apiResource('slides', SlideController::class);
//Route::apiResource('winners', WinnerController::class);

Route::group(
    ['prefix' => 'admin', 'middleware' => ['is_admin']],
    function () {
        Route::post(
            '/clients/{id}/balance',
            ClientUpdateBalanceController::class
        );
        Route::get(
            '/metrics',
            MetricsController::class
        );
        Route::post(
            '/confirm_withdraw',
            ConfirmWithdrawController::class
        );
        Route::post(
            '/wallet_withdraw',
            WalletWithdrawController::class
        );
        Route::post(
            '/wallet_invoice',
            WalletInvoiceController::class
        );
    }
);

Route::prefix('payment')->group(
    function () {
        Route::get(
            '/balance',
            BalanceController::class
        )->middleware('is_admin');
        Route::post(
            '/invoice',
            UserInvoiceController::class
        )->middleware('auth:api');
        Route::get(
            '/payment_status',
            PaymentStatusController::class
        );
        Route::get(
            '/withdraw_status',
            WithdrawStatusController::class
        );
        Route::post(
            '/withdraw',
            UserWithdrawController::class
        )->middleware('auth:api');
        Route::get(
            '/wallet_withdraw_status',
            WalletWithdrawStatusController::class
        );
        Route::get(
            '/wallet_invoice_status',
            WalletInvoiceStatusController::class
        );
    }
);

Route::prefix('cabinet')->group(
    function () {
        Route::get(
            '/games',
            GameController::class
        );
        Route::get(
            '/refresh_games',
            GamesController::class
        );
        Route::post(
            '/start_game',
            StartGameController::class
        )->middleware('auth:api');
        Route::post(
            '/demo_game',
            DemoGameController::class
        );
        Route::post(
            '/close_session',
            CloseSessionController::class
        )->middleware('auth:api');
        Route::get(
            '/strikes',
            StrikeModeController::class
        )->middleware('auth:api');
    }
);

Route::prefix('wallet_service')->group(
    function () {
        Route::post(
            '/GetBalance',
            [WalletServiceController::class, 'GetBalance']
        );
        Route::post(
            '/Withdraw',
            [WalletServiceController::class, 'Withdraw']
        );
        Route::post(
            '/Deposit',
            [WalletServiceController::class, 'Deposit']
        );
        Route::post(
            '/RollbackTransaction',
            [WalletServiceController::class, 'RollbackTransaction']
        );
    }
);

Route::prefix('mancala_service')->group(
    function () {
        Route::post(
            '/Balance',
            [MancalaServiceController::class, 'Balance']
        );
        Route::post(
            '/Credit',
            [MancalaServiceController::class, 'Credit']
        );
        Route::post(
            '/Debit',
            [MancalaServiceController::class, 'Debit']
        );
        Route::post(
            '/Refund',
            [MancalaServiceController::class, 'Refund']
        );
    }
);


Route::prefix('b2bslots_service')->group(
    function () {
        Route::any(
            '/test',
            [B2bslotsTestController::class, 'test']
        );

        Route::post(
            '/debit',
            [B2bslotsServiceController::class, 'debit']
        );

        Route::post(
            '/credit',
            [B2bslotsServiceController::class, 'credit']
        );

        Route::post(
            '/get-features',
            [B2bslotsServiceController::class, 'getFeatures']
        );

        Route::post(
            '/activate-features',
            [B2bslotsServiceController::class, 'activateFeatures']
        );

        Route::post(
            '/update-features',
            [B2bslotsServiceController::class, 'updateFeatures']
        );

        Route::post(
            '/end-features',
            [B2bslotsServiceController::class, 'endFeatures']
        );
    }
);


Route::get(
    '/ref/{code}',
    ReferralController::class
);

Route::group(
    ['prefix' => 'affiliate', 'middleware' => ['auth:api']],
    function () {
        Route::get(
            '/',
            [AffiliateManagerController::class, 'index']
        );
    }
);


