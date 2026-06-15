<?php

namespace App\Http\Responses;

class CommentItemResponse
{
    public $id;

    public $postId;

    public $parentId;

    public $author;

    public $body;

    public $media;

    public $reactionCount;

    public $reactors;

    public $replyCount;

    public $viewerReaction;

    public $createdAt;

    public static function fromJSON(string $json): CommentItemResponse
    {
        return self::fromArray(json_decode($json, true));
    }

    public static function fromArray(array $jsonArray): CommentItemResponse
    {
        $result = new CommentItemResponse;
        if (isset($jsonArray['id'])) {
            $result->id = $jsonArray['id'];
        }
        if (isset($jsonArray['postId'])) {
            $result->postId = $jsonArray['postId'];
        }
        if (array_key_exists('parentId', $jsonArray)) {
            $result->parentId = $jsonArray['parentId'];
        }
        if (isset($jsonArray['author'])) {
            $result->author = \App\Http\Responses\FeedAuthorResponse::fromArray($jsonArray['author']);
        }
        if (isset($jsonArray['body'])) {
            $result->body = $jsonArray['body'];
        }
        if (isset($jsonArray['media'])) {
            $result->media = array_map(
                fn ($item) => \App\Http\Responses\CommentMediaItemResponse::fromArray($item),
                $jsonArray['media']
            );
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
        if (isset($jsonArray['replyCount'])) {
            $result->replyCount = $jsonArray['replyCount'];
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
