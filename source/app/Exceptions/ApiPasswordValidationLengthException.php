<?php

namespace App\Exceptions;

class ApiPasswordValidationLengthException extends ApiException
{
    protected function getErrorParams(): ErrorParameters
    {
        return ErrorParameters::create(-1141, 'ERR_PASSWORD_VALIDATION_LENGTH', false, __('exceptionMessages.ERR_PASSWORD_VALIDATION_LENGTH'));
    }
}
