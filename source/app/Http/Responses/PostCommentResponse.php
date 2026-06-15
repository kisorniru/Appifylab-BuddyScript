<?php

namespace App\Http\Responses;

class PostCommentResponse
{
    public $code;

    public $isSuccess;

    public $message;

    public $responseBody;

    public static function fromJSON(string $json): PostCommentResponse
    {
        return self::fromArray(json_decode($json, true));
    }

    public static function fromArray(array $jsonArray): PostCommentResponse
    {
        $result = new PostCommentResponse;
        if (! isset($jsonArray['code'])) {
            throw new \InvalidArgumentException('Missing the required parameter code when calling PostCommentResponse');
        }
        if (isset($jsonArray['code'])) {
            $result->code = $jsonArray['code'];
        }
        if (! isset($jsonArray['isSuccess'])) {
            throw new \InvalidArgumentException('Missing the required parameter isSuccess when calling PostCommentResponse');
        }
        if (isset($jsonArray['isSuccess'])) {
            $result->isSuccess = $jsonArray['isSuccess'];
        }
        if (! isset($jsonArray['message'])) {
            throw new \InvalidArgumentException('Missing the required parameter message when calling PostCommentResponse');
        }
        if (isset($jsonArray['message'])) {
            $result->message = $jsonArray['message'];
        }
        if (! isset($jsonArray['responseBody'])) {
            throw new \InvalidArgumentException('Missing the required parameter responseBody when calling PostCommentResponse');
        }
        $result->responseBody = \App\Http\Responses\CommentItemResponse::fromArray($jsonArray['responseBody']);

        return $result;
    }
}
