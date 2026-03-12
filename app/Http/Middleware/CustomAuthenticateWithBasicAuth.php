<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Http\Request;

class CustomAuthenticateWithBasicAuth
{
    protected $auth;

    public function __construct(AuthFactory $auth)
    {
        $this->auth = $auth;
    }

    public function handle(Request $request, Closure $next, $guard = null, ...$roles)
    {
        config(['session.expire_on_close' => true]);

        $auth = $this->auth->guard($guard);

        if (! $auth->check()) {
            $auth->basic('username');
        }

        if (! empty($roles)) {
            $user = $auth->user();
            $slug = optional($user->role)->slug;

            if (! in_array($slug, $roles)) {
                $auth->logout();
                abort(401);
            }
        }

        return $next($request);
    }
}
