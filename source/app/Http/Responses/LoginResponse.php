<?php

namespace App\Http\Responses;

class LoginResponse
{
    public $code;

    public $isSuccess;

    public $message;

    public $responseBody;

    public static function fromJSON(string $json): LoginResponse
    {
        return self::fromArray(json_decode($json, true));
    }

    public static function fromArray(array $jsonArray): LoginResponse
    {
        $result = new LoginResponse;
        if (! isset($jsonArray['code'])) {
            throw new \InvalidArgumentException('Missing the required parameter code when calling LoginResponse');
        }
        if (isset($jsonArray['code'])) {
            $result->code = $jsonArray['code'];
        }
        if (! isset($jsonArray['isSuccess'])) {
            throw new \InvalidArgumentException('Missing the required parameter isSuccess when calling LoginResponse');
        }
        if (isset($jsonArray['isSuccess'])) {
            $result->isSuccess = $jsonArray['isSuccess'];
        }
        if (! isset($jsonArray['message'])) {
            throw new \InvalidArgumentException('Missing the required parameter message when calling LoginResponse');
        }
        if (isset($jsonArray['message'])) {
            $result->message = $jsonArray['message'];
        }
        if (! isset($jsonArray['responseBody'])) {
            throw new \InvalidArgumentException('Missing the required parameter responseBody when calling LoginResponse');
        }
        $result->responseBody = \App\Http\Responses\LoginResponseBody::fromArray($jsonArray['responseBody']);

        return $result;
    }
}
