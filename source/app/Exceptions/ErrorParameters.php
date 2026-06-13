<?php

namespace App\Exceptions;

class ErrorParameters
{
    protected $code;

    protected $resultCode;

    protected $isSuccess;

    protected $message;

    protected $httpResponseCode = 400;

    protected $previous = null;

    protected $debugMessage = '';

    public static function create(int $code, string $resultCode, bool $isSuccess, string $message)
    {
        $params = new ErrorParameters;
        $params->code = $code;
        $params->resultCode = $resultCode;
        $params->isSuccess = $isSuccess;
        $params->message = $message ?? $resultCode;

        return $params;
    }

    public function addMessage(string $message)
    {
        $this->message = $message;

        return $this;
    }

    public function addHttpResponseCode(int $httpResponseCode)
    {
        $this->httpResponseCode = $httpResponseCode;

        return $this;
    }

    public function addPreviousException(\Exception $previous)
    {
        $this->previous = $previous;

        return $this;
    }

    public function addDebugMessage($debugMessage)
    {
        $this->debugMessage = $debugMessage;

        return $this;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getIsSuccess()
    {
        return $this->isSuccess;
    }

    public function getResultCode()
    {
        return $this->resultCode;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getHttpResponseCode()
    {
        return $this->httpResponseCode;
    }

    public function getPrevious()
    {
        return $this->previous;
    }

    public function getDebugMessage()
    {
        return $this->debugMessage;
    }
}
