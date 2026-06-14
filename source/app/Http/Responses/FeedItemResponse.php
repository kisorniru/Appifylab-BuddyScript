<?php

namespace App\Http\Responses;

class FeedItemResponse
{
    public $id;

    public $postId;

    public $author;

    public $text;

    public $image;

    public $postType;

    public $visibility;

    public $reactionCount;

    public $reactors;

    public $commentCount;

    public $shareCount;

    public $viewerReaction;

    public $createdAt;

    public static function fromJSON(string $json): FeedItemResponse
    {
        return self::fromArray(json_decode($json, true));
    }

    public static function fromArray(array $jsonArray): FeedItemResponse
    {
        $result = new FeedItemResponse;
        if (isset($jsonArray['id'])) {
            $result->id = $jsonArray['id'];
        }
        if (isset($jsonArray['postId'])) {
            $result->postId = $jsonArray['postId'];
        }
        if (isset($jsonArray['author'])) {
            $result->author = \App\Http\Responses\FeedAuthorResponse::fromArray($jsonArray['author']);
        }
        if (isset($jsonArray['text'])) {
            $result->text = $jsonArray['text'];
        }
        if (array_key_exists('image', $jsonArray)) {
            $result->image = $jsonArray['image'];
        }
        if (isset($jsonArray['postType'])) {
            $result->postType = $jsonArray['postType'];
        }
        if (isset($jsonArray['visibility'])) {
            $result->visibility = $jsonArray['visibility'];
        }
        if (isset($jsonArray['reactionCount'])) {
            $result->reactionCount = $jsonArray['reactionCount'];
        }
        if (isset($jsonArray['reactors'])) {
            $result->reactors = array_map(
                fn ($item) => \App\Http\Responses\FeedAuthorResponse::fromArray($item),
                $jsonArray['reactors']
            );
        }
        if (isset($jsonArray['commentCount'])) {
            $result->commentCount = $jsonArray['commentCount'];
        }
        if (isset($jsonArray['shareCount'])) {
            $result->shareCount = $jsonArray['shareCount'];
        }
        if (array_key_exists('viewerReaction', $jsonArray)) {
            $result->viewerReaction = $jsonArray['viewerReaction'];
        }
        if (isset($jsonArray['createdAt'])) {
            $result->createdAt = $jsonArray['createdAt'];
        }

        return $result;
    }
}
