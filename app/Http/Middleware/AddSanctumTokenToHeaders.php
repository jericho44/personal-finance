<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AddSanctumTokenToHeaders
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->has('token') && empty($request->bearerToken())) {
            $request->headers->set('Authorization', 'Bearer '.$request->token);
        }

        return $next($request);
    }
}
