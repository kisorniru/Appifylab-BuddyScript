<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Throwable;

class Handler extends ExceptionHandler
{
    use JsonErrorTrait;

    protected $dontReport = [
        AuthorizationException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
        'passwordConfirm',
        'credentialKeyId',
        'challengeId',
        'encryptedPassword',
        'encryptedPasswordConfirm',
    ];

    public function render($request, Throwable $exception)
    {
        return $this->handleException($request, $exception);
    }

    public function handleException(Request $request, Throwable $e)
    {
        return match (true) {
            $e instanceof ApiException => $e->renderToJson($request),
            $this->isApiRequest($request) => ApiExceptionFactory::fromException($e)->renderToJson($request, $e),
            default => parent::render($request, $e)
        };
    }

    private function isApiRequest(Request $request): bool
    {
        $currentRoute = Route::current();
        if ($currentRoute) {
            return in_array('api', $currentRoute->gatherMiddleware());
        }

        return $request->is('api/*');
    }
}
