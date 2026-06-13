<?php

namespace App\Exceptions;

class ApiUserAlreadyExistsException extends ApiException
{
    protected function getErrorParams(): ErrorParameters
    {
        return ErrorParameters::create(-1157, 'ERR_USER_ALREADY_EXISTS', false, __('exceptionMessages.ERR_USER_ALREADY_EXISTS'));
    }
}
