<?php

namespace App\Http\Middleware;

use App\Exceptions\AppForbiddenException;
use Closure;

class ValidateClientKey
{
    public function handle($request, Closure $next)
    {
        $client_key = $request->header('X-Client-Key');
        if ($client_key === null) {
            throw new AppForbiddenException;
        }

        $expected_key = config('app.client_secret_key');

        if ($client_key !== $expected_key) {
            throw new AppForbiddenException;
        }

        return $next($request);
    }
}
