<?php

namespace App\Http\Middleware;

use App\Helpers\ResponseFormatter;
use Closure;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Http\Request;

class RoleAuthorization
{
    protected $auth;

    public function __construct(AuthFactory $auth)
    {
        $this->auth = $auth;
    }

    public function handle(Request $request, Closure $next, ...$roles)
    {
        $authGuard = $this->auth->guard();

        if ($authGuard->check()) {
            $roleAuth = $authGuard->user()->role;

            if (count($roles) > 0) {
                if (in_array($roleAuth->slug, $roles)) {
                    return $next($request);
                } else {
                    return ResponseFormatter::error(null, 'Role '.$roleAuth->name.' you are not allowed to access this URL!', 403);
                }
            } else {
                return $next($request);
            }
        } else {
            return ResponseFormatter::error(null, 'Unauthorized', 401);
        }
    }
}
