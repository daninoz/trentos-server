<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Hash;
use Config;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use GuzzleHttp;
use App\User;

class AuthController extends Controller {

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Generate JSON Web Token.
     */
    protected function createToken($user)
    {
        $payload = [
            'sub' => $user->id,
            'iat' => time(),
            'exp' => time() + (2 * 7 * 24 * 60 * 60)
        ];
        return JWT::encode($payload, env('app.token_secret'));
    }

    /**
     * Login with Facebook.
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        $user = User::where('email', '=', $credentials['email'])->firstOrFail();

        if (!app('hash')->check($credentials['password'], $user->password)) {
            abort(501);
        }

        return response()->json(['token' => $this->createToken($user)]);
    }

    public function register(Request $request)
    {
        try {
            $this->userService->validateInput($request->all());
        } catch (\Exception $e) {
            abort(422);
        }

        $response = $this->userService->create($request);

        return response()->json($response);
    }
}