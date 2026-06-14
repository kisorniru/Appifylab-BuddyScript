<?php

namespace App\Exceptions;

class ApiInvalidGenderTypeException extends ApiException
{
    protected function getErrorParams(): ErrorParameters
    {
        return ErrorParameters::create(-1115, 'ERR_INVALID_GENDER_TYPE', false, __('exceptionMessages.ERR_INVALID_GENDER_TYPE'));
    }
}
