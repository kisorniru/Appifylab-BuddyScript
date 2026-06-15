<?php

namespace App\Http\Responses;

class PostReactionUserResponse
{
    public $code;

    public $isSuccess;

    public $message;

    public $responseBody;

    public static function fromJSON(string $json): PostReactionUserResponse
    {
        return self::fromArray(json_decode($json, true));
    }

    public static function fromArray(array $jsonArray): PostReactionUserResponse
    {
        $result = new PostReactionUserResponse;
        if (isset($jsonArray['code'])) {
            $result->code = $jsonArray['code'];
        }
        if (isset($jsonArray['isSuccess'])) {
            $result->isSuccess = $jsonArray['isSuccess'];
        }
        if (isset($jsonArray['message'])) {
            $result->message = $jsonArray['message'];
        }
        if (isset($jsonArray['responseBody'])) {
            $result->responseBody = PostReactionUserResponseBody::fromArray($jsonArray['responseBody']);
        }

        return $result;
    }
}
