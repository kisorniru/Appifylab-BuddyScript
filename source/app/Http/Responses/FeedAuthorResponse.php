<?php

namespace App\Http\Responses;

class FeedAuthorResponse
{
    public $id;

    public $name;

    public $avatar;

    public static function fromJSON(string $json): FeedAuthorResponse
    {
        return self::fromArray(json_decode($json, true));
    }

    public static function fromArray(array $jsonArray): FeedAuthorResponse
    {
        $result = new FeedAuthorResponse;
        if (isset($jsonArray['id'])) {
            $result->id = $jsonArray['id'];
        }
        if (isset($jsonArray['name'])) {
            $result->name = $jsonArray['name'];
        }
        if (isset($jsonArray['avatar'])) {
            $result->avatar = $jsonArray['avatar'];
        }

        return $result;
    }
}
