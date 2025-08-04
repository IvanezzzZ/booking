<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class AuthApiToken
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();

        if (! $token) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user = User::query()
            ->where('api_token', $token)
            ->first();

        if (! $user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        auth()->login($user);

        return $next($request);
    }
}
