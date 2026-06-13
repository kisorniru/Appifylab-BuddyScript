<?php

namespace App\Http\Responses;

class ErrorResult
{
    public $code;

    public $isSuccess;

    public $message;

    public static function fromJSON(string $json): ErrorResult
    {
        return self::fromArray(json_decode($json, true));
    }

    public static function fromArray(array $jsonArray): ErrorResult
    {
        $result = new ErrorResult;
        if (! isset($jsonArray['code'])) {
            throw new \InvalidArgumentException('Missing the required parameter code when calling ErrorResult');
        }
        if (isset($jsonArray['code'])) {
            $result->code = $jsonArray['code'];
        }
        if (! isset($jsonArray['isSuccess'])) {
            throw new \InvalidArgumentException('Missing the required parameter isSuccess when calling ErrorResult');
        }
        if (isset($jsonArray['isSuccess'])) {
            $result->isSuccess = $jsonArray['isSuccess'];
        }
        if (! isset($jsonArray['message'])) {
            throw new \InvalidArgumentException('Missing the required parameter message when calling ErrorResult');
        }
        if (isset($jsonArray['message'])) {
            $result->message = $jsonArray['message'];
        }

        return $result;
    }
}
