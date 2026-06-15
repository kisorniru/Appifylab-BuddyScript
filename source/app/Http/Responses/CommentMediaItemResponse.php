<?php

namespace App\Http\Responses;

class CommentMediaItemResponse
{
    public $id;

    public $mediaType;

    public $fileUrl;

    public $thumbnailUrl;

    public $mimeType;

    public $duration;

    public static function fromJSON(string $json): CommentMediaItemResponse
    {
        return self::fromArray(json_decode($json, true));
    }

    public static function fromArray(array $jsonArray): CommentMediaItemResponse
    {
        $result = new CommentMediaItemResponse;
        if (isset($jsonArray['id'])) {
            $result->id = $jsonArray['id'];
        }
        if (isset($jsonArray['mediaType'])) {
            $result->mediaType = $jsonArray['mediaType'];
        }
        if (isset($jsonArray['fileUrl'])) {
            $result->fileUrl = $jsonArray['fileUrl'];
        }
        if (array_key_exists('thumbnailUrl', $jsonArray)) {
            $result->thumbnailUrl = $jsonArray['thumbnailUrl'];
        }
        if (array_key_exists('mimeType', $jsonArray)) {
            $result->mimeType = $jsonArray['mimeType'];
        }
        if (array_key_exists('duration', $jsonArray)) {
            $result->duration = $jsonArray['duration'];
        }

        return $result;
    }
}
