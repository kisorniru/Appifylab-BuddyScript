<?php

namespace App\Exceptions;

class ApiInvalidArgumentException extends ApiException
{
    protected $validationMessages;

    public function __construct($validationMessages = null)
    {
        $this->validationMessages = $validationMessages;
        parent::__construct();
    }

    protected function getErrorParams(): ErrorParameters
    {
        $message = $this->validationMessages ? $this->formatMessages($this->validationMessages) : __('exceptionMessages.ERR_INVALID_ARGUMENT');

        return ErrorParameters::create(-1109, 'ERR_INVALID_ARGUMENT', false, $message);
    }

    private function formatMessages($messages)
    {
        if (is_array($messages)) {
            $flat = [];
            array_walk_recursive($messages, function ($a) use (&$flat) {
                $flat[] = $a;
            });

            return implode(' ', $flat);
        }

        return (string) $messages;
    }
}
