<?php

namespace App\Http\Responses;

class FeedResponse
{
    public $code;

    public $isSuccess;

    public $message;

    public $responseBody;

    public static function fromJSON(string $json): FeedResponse
    {
        return self::fromArray(json_decode($json, true));
    }

    public static function fromArray(array $jsonArray): FeedResponse
    {
        $result = new FeedResponse;
        if (! isset($jsonArray['code'])) {
            throw new \InvalidArgumentException('Missing the required parameter code when calling FeedResponse');
        }
        if (isset($jsonArray['code'])) {
            $result->code = $jsonArray['code'];
        }
        if (! isset($jsonArray['isSuccess'])) {
            throw new \InvalidArgumentException('Missing the required parameter isSuccess when calling FeedResponse');
        }
        if (isset($jsonArray['isSuccess'])) {
            $result->isSuccess = $jsonArray['isSuccess'];
        }
        if (! isset($jsonArray['message'])) {
            throw new \InvalidArgumentException('Missing the required parameter message when calling FeedResponse');
        }
        if (isset($jsonArray['message'])) {
            $result->message = $jsonArray['message'];
        }
        if (! isset($jsonArray['responseBody'])) {
            throw new \InvalidArgumentException('Missing the required parameter responseBody when calling FeedResponse');
        }
        $result->responseBody = \App\Http\Responses\FeedResponseBody::fromArray($jsonArray['responseBody']);

        return $result;
    }
}
