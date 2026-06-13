<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as BaseAuthenticate;

class Authenticate extends BaseAuthenticate
{
    protected function redirectTo($request)
    {
        if (in_array('auth:user', $request->route()->middleware()) && ! $this->auth->guard('user')->check()) {
            return route('user.login');
        }

        if ($request->expectsJson() || $request->is('api/*')) {
            return null;
        }

    }
}
