<?php

namespace App\Http\Responses;

class FeedMetaResponse
{
    public $next_cursor;

    public $has_more;

    public $unread_count;

    public static function fromJSON(string $json): FeedMetaResponse
    {
        return self::fromArray(json_decode($json, true));
    }

    public static function fromArray(array $jsonArray): FeedMetaResponse
    {
        $result = new FeedMetaResponse;
        if (array_key_exists('next_cursor', $jsonArray)) {
            $result->next_cursor = $jsonArray['next_cursor'];
        }
        if (isset($jsonArray['has_more'])) {
            $result->has_more = $jsonArray['has_more'];
        }
        if (isset($jsonArray['unread_count'])) {
            $result->unread_count = $jsonArray['unread_count'];
        }

        return $result;
    }
}
