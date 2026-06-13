<?php

namespace App\Exceptions;

class AppForbiddenException extends ApiException
{
    protected function getErrorParams(): ErrorParameters
    {
        return ErrorParameters::create(-1166, 'ERR_FORBIDDEN', false, __('exceptionMessages.ERR_FORBIDDEN'))
            ->addHttpResponseCode(403);
    }
}
