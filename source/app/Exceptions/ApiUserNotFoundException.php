<?php

namespace App\Exceptions;

class ApiUserNotFoundException extends ApiException
{
    protected function getErrorParams(): ErrorParameters
    {
        return ErrorParameters::create(-1164, 'ERR_USER_NOT_FOUND', false, __('exceptionMessages.ERR_USER_NOT_FOUND'));
    }
}
