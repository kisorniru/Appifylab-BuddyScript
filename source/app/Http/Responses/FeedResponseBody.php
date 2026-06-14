<?php

namespace App\Http\Responses;

class FeedResponseBody
{
    public $items;

    public $meta;

    public static function fromJSON(string $json): FeedResponseBody
    {
        return self::fromArray(json_decode($json, true));
    }

    public static function fromArray(array $jsonArray): FeedResponseBody
    {
        $result = new FeedResponseBody;
        if (isset($jsonArray['items'])) {
            $result->items = array_map(
                fn ($item) => \App\Http\Responses\FeedItemResponse::fromArray($item),
                $jsonArray['items']
            );
        }
        if (isset($jsonArray['meta'])) {
            $result->meta = \App\Http\Responses\FeedMetaResponse::fromArray($jsonArray['meta']);
        }

        return $result;
    }
}
