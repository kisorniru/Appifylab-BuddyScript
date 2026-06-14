<?php

namespace App\Exceptions;

class ApiValidatePasswordException extends ApiException
{
    protected function getErrorParams(): ErrorParameters
    {
        return ErrorParameters::create(-1165, 'ERR_VALIDATE_PASSWORD', false, __($this->getMessage() ?: 'exceptionMessages.ERR_VALIDATE_PASSWORD'));
    }
}
