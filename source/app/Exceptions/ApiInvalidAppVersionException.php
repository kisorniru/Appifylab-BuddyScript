<?php

namespace App\Exceptions;

class ApiInvalidAppVersionException extends ApiException
{
    protected function getErrorParams(): ErrorParameters
    {
        return ErrorParameters::create(-1108, 'ERR_INVALID_APP_VERSION', false, __('exceptionMessages.ERR_INVALID_APP_VERSION'));
    }
}
