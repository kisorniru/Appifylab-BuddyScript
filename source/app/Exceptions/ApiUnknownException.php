<?php

namespace App\Exceptions;

class ApiUnknownException extends ApiException
{
    protected function getErrorParams(): ErrorParameters
    {
        return ErrorParameters::create(-1155, 'ERR_UNKNOWN', false, __('exceptionMessages.ERR_UNKNOWN'));
    }
}
