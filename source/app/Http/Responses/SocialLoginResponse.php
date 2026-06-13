<?php

namespace App\Http\Responses;

class SocialLoginResponse
{
    public $code;

    public $isSuccess;

    public $message;

    public $responseBody;

    public static function fromJSON(string $json): SocialLoginResponse
    {
        return self::fromArray(json_decode($json, true));
    }

    public static function fromArray(array $jsonArray): SocialLoginResponse
    {
        $result = new SocialLoginResponse;
        if (! isset($jsonArray['code'])) {
            throw new \InvalidArgumentException('Missing the required parameter code when calling SocialLoginResponse');
        }
        if (isset($jsonArray['code'])) {
            $result->code = $jsonArray['code'];
        }
        if (! isset($jsonArray['isSuccess'])) {
            throw new \InvalidArgumentException('Missing the required parameter isSuccess when calling SocialLoginResponse');
        }
        if (isset($jsonArray['isSuccess'])) {
            $result->isSuccess = $jsonArray['isSuccess'];
        }
        if (! isset($jsonArray['message'])) {
            throw new \InvalidArgumentException('Missing the required parameter message when calling SocialLoginResponse');
        }
        if (isset($jsonArray['message'])) {
            $result->message = $jsonArray['message'];
        }
        if (! isset($jsonArray['responseBody'])) {
            throw new \InvalidArgumentException('Missing the required parameter responseBody when calling SocialLoginResponse');
        }
        $result->responseBody = \App\Http\Responses\SocialLoginResponseBody::fromArray($jsonArray['responseBody']);

        return $result;
    }
}
