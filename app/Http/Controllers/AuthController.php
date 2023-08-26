<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Jobs\CreateIdentityJob;
use App\Services\Referral\ReferralService;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'googleLogin', 'oneClick']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);

        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register()
    {
        $request = request();
        $validator = Validator::make($request->all(), [
            'login'       => 'required|string|between:2,100',
            'email'       => 'required|string|email|max:100|unique:users',
            'fingerprint' => 'required|string',
            'currency'    => ['required', Rule::in(config('enums.user_currency'))],
            'birth_date'  => 'nullable|date',
            'first_name'  => 'nullable|string',
            'last_name'   => 'nullable|string',
            'gender'      => ['nullable', Rule::in(['M', 'F'])],
            'patronymic'  => 'nullable|string',
            'phone'       => 'nullable|string',
            'address'     => 'nullable|string',
            'country'     => 'nullable|string',
            'password'    => [
                'required',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised()
            ],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        $user = User::create(
            array_merge(
                $validator->validated(),
                [
                    'password'    => bcrypt($request->password),
                ]
            )
        );
        $user->identity = md5($validator->validated()['email']);
        $user->save();

        $referrerId = request()->cookie('referrer_id');
        if ($referrerId && is_numeric($referrerId)) {
            ReferralService::createReferral((int) $referrerId, $user->id);
        }

        dispatch(new CreateIdentityJob([
            'name'     => md5($validator->validated()['email']),
            'currency' => $user->currency
        ]));

        return response()->json(
            [
                'message' => 'User successfully registered',
                'user' => $user
            ],
            201
        );
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }

    public function googleLogin(Request $request)
    {
        $request = request();
        $validator = Validator::make($request->all(), [
            'access_token' => 'required|string',
            'fingerprint'  => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $data = $validator->validated();
        $provider = "google";
        $token = $data['access_token'];
        $fingerprint = $data['fingerprint'];

        $providerUser = Socialite::driver($provider)->userFromToken($token);
        $user = User::where('google_id', '=', $providerUser->id)->first();

        if ($user == null) {
            $user = User::create([
                'login'       => $providerUser->name,
                'email'       => $providerUser->email,
                'google_id'   => $providerUser->id,
                'fingerprint' => $fingerprint,
                'password'    => Str::random(15),
                'currency'    => 'KZT',
            ]);

            $referrerId = request()->cookie('referrer_id');
            if ($referrerId && is_numeric($referrerId)) {
                ReferralService::createReferral((int) $referrerId, $user->id);
            }
        }

        $token = auth()->login($user);

        return response()->json([
            'success' => true,
            'token' => $token
        ]);
    }

    public function oneClick()
    {
        $request = request();
        $validator = Validator::make($request->all(), [
            'currency'    => ['required', Rule::in(config('enums.user_currency'))],
            'country'     => 'nullable|string',
            'fingerprint' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $login = Str::random(7);
        $password = Str::random(15);
        $email = Str::random(15) . '@wonorado.org';

        $user = User::create(
            array_merge(
                $validator->validated(),
                [
                    'login'       => $login,
                    'password'    => bcrypt($password),
                    'email'       => $email,
                ]
            )
        );
        $user->identity = md5($email);
        $user->save();

        $referrerId = request()->cookie('referrer_id');
        if ($referrerId && is_numeric($referrerId)) {
            ReferralService::createReferral((int) $referrerId, $user->id);
        }

        dispatch(new CreateIdentityJob([
            'name'     => md5($email),
            'currency' => $user->currency
        ]));

        $credentials = [
            'email'    => $email,
            'password' => $password
        ];
        $token = auth()->attempt($credentials);

        return response()->json(
            [
                'message'  => 'User successfully registered',
                'email'    => $email,
                'password' => $password,
                'token'    => $token
            ],
            201
        );
    }
}
