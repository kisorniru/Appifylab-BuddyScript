<?php

namespace App\Exceptions;

class ApiInvalidImageFileTypeException extends ApiException
{
    protected function getErrorParams(): ErrorParameters
    {
        return ErrorParameters::create(-1116, 'ERR_INVALID_IMAGE_FILE_TYPE', false, __('exceptionMessages.ERR_INVALID_IMAGE_FILE_TYPE'));
    }
}
