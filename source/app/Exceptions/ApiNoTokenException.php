<?php

namespace App\Exceptions;

class ApiNoTokenException extends ApiException
{
    protected function getErrorParams(): ErrorParameters
    {
        return ErrorParameters::create(-1134, 'ERR_NO_API_TOKEN', false, __('exceptionMessages.ERR_NO_API_TOKEN'));
    }
}
