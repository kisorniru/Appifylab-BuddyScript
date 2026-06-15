<?php

namespace App\Http\Responses;

class PostReactionUserResponseBody
{
    public $items;

    public $meta;

    public static function fromJSON(string $json): PostReactionUserResponseBody
    {
        return self::fromArray(json_decode($json, true));
    }

    public static function fromArray(array $jsonArray): PostReactionUserResponseBody
    {
        $result = new PostReactionUserResponseBody;
        if (isset($jsonArray['items'])) {
            $result->items = array_map(
                fn ($item) => PostReactionUserItemResponse::fromArray($item),
                $jsonArray['items']
            );
        }
        if (isset($jsonArray['meta'])) {
            $result->meta = FeedMetaResponse::fromArray($jsonArray['meta']);
        }

        return $result;
    }
}
