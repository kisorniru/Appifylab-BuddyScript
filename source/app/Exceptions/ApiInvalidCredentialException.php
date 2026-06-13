<?php

namespace App\Exceptions;

class ApiInvalidCredentialException extends ApiException
{
    protected function getErrorParams(): ErrorParameters
    {
        return ErrorParameters::create(-1168, 'ERR_INVALID_CREDENTIALS', false, __('exceptionMessages.ERR_INVALID_CREDENTIALS'));
    }
}
