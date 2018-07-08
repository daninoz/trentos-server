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

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        $user = User::where('email', '=', $credentials['email'])->firstOrFail();

        if (!app('hash')->check($credentials['password'], $user->password)) {
            abort(501);
        }

        return response()->json(['token' => $this->createToken($user)]);
    }

    /**
     * Login with Facebook.
     */
    public function facebook(Request $request)
    {
        $client = new GuzzleHttp\Client();
        $params = [
            'code' => $request->input('code'),
            'client_id' => $request->input('clientId'),
            'redirect_uri' => $request->input('redirectUri'),
            'client_secret' => env('app.facebook_secret')
        ];
        // Step 1. Exchange authorization code for access token.
        $accessTokenResponse = $client->request('GET', 'https://graph.facebook.com/v2.7/oauth/access_token', [
            'query' => $params
        ]);
        $accessToken = json_decode($accessTokenResponse->getBody(), true);

        // Step 2. Retrieve profile information about the current user.
        $fields = 'id,email,first_name,last_name,link,name';
        $profileResponse = $client->request('GET', 'https://graph.facebook.com/v2.7/me', [
            'query' => [
                'access_token' => $accessToken['access_token'],
                'fields' => $fields
            ]
        ]);
        $profile = json_decode($profileResponse->getBody(), true);

        // If the user was already registerd, with log him
        $user = User::where('facebook', '=', $profile['id'])->first();
        if ($user)
        {
            return response()->json(['token' => $this->createToken($user)]);
        }

        // If the user was registered but not with facebook, we update her
        $user = User::where('email', '=', $profile['email'])->first();
        if ($user)
        {
            $user->facebook = $profile['id'];
            $user->save();
            return response()->json(['token' => $this->createToken($user)]);
        }

        // If the user is not registerd, we do that
        $user = new User;
        $user->facebook = $profile['id'];
        $user->email = $profile['email'];
        $user->name = $profile['name'];
        $user->save();
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