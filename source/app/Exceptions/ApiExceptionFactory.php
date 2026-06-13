<?php

namespace App\Exceptions;

use Illuminate\Validation\ValidationException;

class ApiExceptionFactory
{
    protected function __construct() {}

    public static function fromException(\Throwable $ex): ApiException
    {
        if ($ex instanceof \PDOException) {
            return new ApiUnknownException('Database error occurred: '.$ex->getMessage());

        } elseif ($ex instanceof ValidationException) {
            $messages = method_exists($ex, 'errors') ? $ex->errors() : $ex->getMessage();

            return new ApiInvalidArgumentException($messages);
        } elseif ($ex instanceof \Illuminate\Auth\AuthenticationException) {
            return new ApiNoTokenException;
        } else {
            return new ApiUnknownException($ex->getMessage());
        }
    }
}
