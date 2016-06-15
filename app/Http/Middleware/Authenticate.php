<?php

namespace App\Http\Middleware;

use Closure;
use Firebase\JWT\JWT;

class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->header('Authorization')) {
            $token = explode(' ', $request->header('Authorization'))[1];
            $payload = (array) JWT::decode($token, env('app.token_secret'), array('HS256'));
            if ($payload['exp'] < time())
            {
                return response()->json(['message' => 'Token has expired']);
            }
            $request['user'] = $payload;
            return $next($request);
        } else {
            return response()->json(['message' => 'Please make sure your request has an Authorization header'], 401);
        }
    }
}
