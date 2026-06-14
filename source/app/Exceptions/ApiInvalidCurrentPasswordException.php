<?php

namespace App\Exceptions;

class ApiInvalidCurrentPasswordException extends ApiException
{
    protected function getErrorParams(): ErrorParameters
    {
        return ErrorParameters::create(-1112, 'ERR_INVALID_CURRENT_PASSWORD', false, __('exceptionMessages.ERR_INVALID_CURRENT_PASSWORD'));
    }
}
