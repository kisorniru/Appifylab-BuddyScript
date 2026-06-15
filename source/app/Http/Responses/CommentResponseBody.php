<?php

namespace App\Http\Responses;

class CommentResponseBody
{
    public $items;

    public $meta;

    public static function fromJSON(string $json): CommentResponseBody
    {
        return self::fromArray(json_decode($json, true));
    }

    public static function fromArray(array $jsonArray): CommentResponseBody
    {
        $result = new CommentResponseBody;
        if (isset($jsonArray['items'])) {
            $result->items = array_map(
                fn ($item) => \App\Http\Responses\CommentItemResponse::fromArray($item),
                $jsonArray['items']
            );
        }
        if (isset($jsonArray['meta'])) {
            $result->meta = \App\Http\Responses\FeedMetaResponse::fromArray($jsonArray['meta']);
        }

        return $result;
    }
}
