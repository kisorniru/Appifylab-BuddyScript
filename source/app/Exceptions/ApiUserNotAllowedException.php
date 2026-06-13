<?php

namespace App\Exceptions;

class ApiUserNotAllowedException extends ApiException
{
    protected function getErrorParams(): ErrorParameters
    {
        return ErrorParameters::create(-1163, 'ERR_USER_NOT_ALLOWED', false, __('exceptionMessages.ERR_USER_NOT_ALLOWED'));
    }
}
