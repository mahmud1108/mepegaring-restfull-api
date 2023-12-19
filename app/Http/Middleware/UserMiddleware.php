<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Dotenv\Repository\RepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('Authorization');
        $auth = true;

        if (!$token) {
            $auth = false;
        }

        $user = User::where('token', $token)->first();
        if (!$user) {
            $auth = false;
        } else {
            Auth::login($user);
        }

        if ($auth) {
            return $next($request);
        } else {
            return response()->json([
                'errors' => [
                    'message' => [
                        'Unauthorized'
                    ]
                ]
            ])->setStatusCode(401);
        }
    }
}
