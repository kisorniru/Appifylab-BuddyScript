<?php

namespace App\Exceptions;

class ApiConfirmPasswordNotMatchException extends ApiException
{
    protected function getErrorParams(): ErrorParameters
    {
        return ErrorParameters::create(-1104, 'ERR_CONFIRM_PASSWORD_NOT_MATCH', false, __('exceptionMessages.ERR_CONFIRM_PASSWORD_NOT_MATCH'));
    }
}
