<?php

namespace App\Http\Responses;

class PostReactionUserItemResponse
{
    public $id;

    public $user;

    public $reactionType;

    public $reactedAt;

    public static function fromJSON(string $json): PostReactionUserItemResponse
    {
        return self::fromArray(json_decode($json, true));
    }

    public static function fromArray(array $jsonArray): PostReactionUserItemResponse
    {
        $result = new PostReactionUserItemResponse;
        if (isset($jsonArray['id'])) {
            $result->id = $jsonArray['id'];
        }
        if (isset($jsonArray['user'])) {
            $result->user = FeedAuthorResponse::fromArray($jsonArray['user']);
        }
        if (isset($jsonArray['reactionType'])) {
            $result->reactionType = $jsonArray['reactionType'];
        }
        if (isset($jsonArray['reactedAt'])) {
            $result->reactedAt = $jsonArray['reactedAt'];
        }

        return $result;
    }
}
