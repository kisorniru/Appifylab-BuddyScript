<?php

namespace App\Exceptions;

class ParameterException extends ApiException
{
    protected function getErrorParams(): ErrorParameters
    {
        return ErrorParameters::create(-1167, 'PARAMETER', false, 'ParameterException');
    }
}
