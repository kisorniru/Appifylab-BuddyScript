<?php

namespace App\Exceptions;

class ApiInvalidFileSizeException extends ApiException
{
    protected function getErrorParams(): ErrorParameters
    {
        return ErrorParameters::create(-1113, 'ERR_INVALID_FILE_SIZE', false, __('exceptionMessages.ERR_INVALID_FILE_SIZE'));
    }
}
