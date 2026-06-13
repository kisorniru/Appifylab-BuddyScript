<?php

namespace App\Exceptions;

abstract class ApiException extends \Exception
{
    use JsonErrorTrait;

    public function renderToJson($request, $original_exception = null)
    {
        return $this->toJsonResponse($request, $original_exception ?? $this, $this->getErrorParams());
    }

    abstract protected function getErrorParams(): ErrorParameters;
}
