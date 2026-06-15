<?php

namespace App\Http\Responses;

class NotificationResponse
{
    public $code;

    public $isSuccess;

    public $message;

    public $responseBody;

    public static function fromJSON(string $json): NotificationResponse
    {
        return self::fromArray(json_decode($json, true));
    }

    public static function fromArray(array $jsonArray): NotificationResponse
    {
        $result = new NotificationResponse;
        foreach (['code', 'isSuccess', 'message'] as $key) {
            if (isset($jsonArray[$key])) {
                $result->{$key} = $jsonArray[$key];
            }
        }
        if (isset($jsonArray['responseBody'])) {
            $result->responseBody = \App\Http\Responses\NotificationResponseBody::fromArray($jsonArray['responseBody']);
        }

        return $result;
    }
}
