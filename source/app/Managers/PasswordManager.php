<?php

namespace App\Managers;

use App\Exceptions\ApiConfirmPasswordNotMatchException;
use App\Exceptions\ApiPasswordValidationException;
use App\Exceptions\ApiPasswordValidationLengthException;

class PasswordManager
{
    public static function validatePassword(string $password, string $passwordConfirm): void
    {
        if (strlen($password) < 8) {
            throw new ApiPasswordValidationLengthException;
        }

        if (! preg_match('/[A-Z]/', $password) || ! preg_match('/[a-z]/', $password) || ! preg_match('/[^a-zA-Z0-9]/', $password)) {
            throw new ApiPasswordValidationException;
        }

        if ($password !== $passwordConfirm) {
            throw new ApiConfirmPasswordNotMatchException;
        }
    }
}
