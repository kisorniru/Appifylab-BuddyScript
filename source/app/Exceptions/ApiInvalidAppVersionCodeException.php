<?php

namespace App\Exceptions;

class ApiInvalidAppVersionCodeException extends ApiException
{
    protected function getErrorParams(): ErrorParameters
    {
        return ErrorParameters::create(-1107, 'ERR_INVALID_APP_VERSION_CODE', false, __('exceptionMessages.ERR_INVALID_APP_VERSION_CODE'));
    }
}
