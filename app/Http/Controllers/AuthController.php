<?php

namespace App\Http\Controllers;

use Hash;
use Config;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use GuzzleHttp;
use App\User;

class AuthController extends Controller {

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
        $accessTokenResponse = $client->request('GET', 'https://graph.facebook.com/v2.6/oauth/access_token', [
            'query' => $params
        ]);
        $accessToken = json_decode($accessTokenResponse->getBody(), true);

        // Step 2. Retrieve profile information about the current user.
        $fields = 'id,email,first_name,last_name,link,name';
        $profileResponse = $client->request('GET', 'https://graph.facebook.com/v2.6/me', [
            'query' => [
                'access_token' => $accessToken['access_token'],
                'fields' => $fields
            ]
        ]);
        $profile = json_decode($profileResponse->getBody(), true);

        // Step 3a. If user is already signed in then link accounts.
        if ($request->header('Authorization'))
        {
            $user = User::where('facebook', '=', $profile['id']);

            if ($user->first())
            {
                return response()->json(['message' => 'There is already a Facebook account that belongs to you'], 409);
            }

            $token = explode(' ', $request->header('Authorization'))[1];
            $payload = (array) JWT::decode($token, env('app.token_secret'), array('HS256'));

            $user = User::find($payload['sub']);
            $user->facebook = $profile['id'];
            $user->email = $user->email ?: $profile['email'];
            $user->displayName = $user->displayName ?: $profile['name'];
            $user->save();

            return response()->json(['token' => $this->createToken($user)]);
        }
        // Step 3b. Create a new user account or return an existing one.
        else
        {
            $user = User::where('facebook', '=', $profile['id']);

            if ($user->first())
            {
                return response()->json(['token' => $this->createToken($user->first())]);
            }

            $user = new User;
            $user->facebook = $profile['id'];
            $user->email = $profile['email'];
            $user->displayName = $profile['name'];
            $user->is_adim = 1;
            $user->save();

            return response()->json(['token' => $this->createToken($user)]);
        }
    }
}