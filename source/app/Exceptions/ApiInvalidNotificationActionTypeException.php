<?php

namespace App\Exceptions;

class ApiInvalidNotificationActionTypeException extends ApiException
{
    protected function getErrorParams(): ErrorParameters
    {
        return ErrorParameters::create(-1118, 'ERR_INVALID_NOTIFICATION_ACTION_TYPE', false, __('exceptionMessages.ERR_INVALID_NOTIFICATION_ACTION_TYPE'));
    }
}
