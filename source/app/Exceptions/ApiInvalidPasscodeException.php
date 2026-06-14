<?php

namespace App\Exceptions;

class ApiInvalidPasscodeException extends ApiException
{
    protected function getErrorParams(): ErrorParameters
    {
        return ErrorParameters::create(-1124, 'ERR_INVALID_PASSCODE', false, __('exceptionMessages.ERR_INVALID_PASSCODE'));
    }
}
