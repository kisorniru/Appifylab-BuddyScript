<?php

namespace App\Exceptions;

class ApiUserAccountStatusInactiveException extends ApiException
{
    protected function getErrorParams(): ErrorParameters
    {
        return ErrorParameters::create(-1170, 'ERR_USER_ACCOUNT_STATUS_INACTIVE', false, __('exceptionMessages.ERR_USER_ACCOUNT_STATUS_INACTIVE'));
    }
}
