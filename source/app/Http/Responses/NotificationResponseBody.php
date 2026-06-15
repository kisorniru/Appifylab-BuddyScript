<?php

namespace App\Http\Responses;

class NotificationResponseBody
{
    public $items;

    public $meta;

    public static function fromJSON(string $json): NotificationResponseBody
    {
        return self::fromArray(json_decode($json, true));
    }

    public static function fromArray(array $jsonArray): NotificationResponseBody
    {
        $result = new NotificationResponseBody;
        if (isset($jsonArray['items'])) {
            $result->items = array_map(
                fn ($item) => \App\Http\Responses\NotificationItemResponse::fromArray($item),
                $jsonArray['items']
            );
        }
        if (isset($jsonArray['meta'])) {
            $result->meta = \App\Http\Responses\FeedMetaResponse::fromArray($jsonArray['meta']);
        }

        return $result;
    }
}
