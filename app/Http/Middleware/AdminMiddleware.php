<?php

namespace App\Http\Middleware;

use App\Models\Admin;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
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

        $admin = Admin::where('token', $token)->first();
        if (!$admin) {
            $auth = false;
        } else {
            Auth::login($admin);
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
