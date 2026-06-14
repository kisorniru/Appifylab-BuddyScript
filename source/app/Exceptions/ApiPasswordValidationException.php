<?php

namespace App\Exceptions;

class ApiPasswordValidationException extends ApiException
{
    protected function getErrorParams(): ErrorParameters
    {
        return ErrorParameters::create(-1140, 'ERR_PASSWORD_VALIDATION', false, __('exceptionMessages.ERR_PASSWORD_VALIDATION'));
    }
}
