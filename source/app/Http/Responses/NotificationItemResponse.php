<?php

namespace App\Http\Responses;

class NotificationItemResponse
{
    public $id;

    public $type;

    public $sender;

    public $message;

    public $notifiableType;

    public $notifiableId;

    public $data;

    public $isRead;

    public $readAt;

    public $createdAt;

    public static function fromJSON(string $json): NotificationItemResponse
    {
        return self::fromArray(json_decode($json, true));
    }

    public static function fromArray(array $jsonArray): NotificationItemResponse
    {
        $result = new NotificationItemResponse;
        foreach (['id', 'type', 'message', 'notifiableType', 'notifiableId', 'data', 'isRead', 'readAt', 'createdAt'] as $key) {
            if (array_key_exists($key, $jsonArray)) {
                $result->{$key} = $jsonArray[$key];
            }
        }
        if (isset($jsonArray['sender'])) {
            $result->sender = \App\Http\Responses\FeedAuthorResponse::fromArray($jsonArray['sender']);
        }

        return $result;
    }
}
