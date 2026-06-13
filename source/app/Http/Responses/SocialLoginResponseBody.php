<?php

namespace App\Http\Responses;

class SocialLoginResponseBody
{
    public $id;

    public $name;

    public $email;

    public $firstTimeLogin;

    public $apiToken;

    public static function fromJSON(string $json): SocialLoginResponseBody
    {
        return self::fromArray(json_decode($json, true));
    }

    public static function fromArray(array $jsonArray): SocialLoginResponseBody
    {
        $result = new SocialLoginResponseBody;
        if (! isset($jsonArray['id'])) {
            throw new \InvalidArgumentException('Missing the required parameter id when calling SocialLoginResponseBody');
        }
        if (isset($jsonArray['id'])) {
            $result->id = $jsonArray['id'];
        }
        if (! isset($jsonArray['name'])) {
            throw new \InvalidArgumentException('Missing the required parameter name when calling SocialLoginResponseBody');
        }
        if (isset($jsonArray['name'])) {
            $result->name = $jsonArray['name'];
        }
        if (! isset($jsonArray['email'])) {
            throw new \InvalidArgumentException('Missing the required parameter email when calling SocialLoginResponseBody');
        }
        if (isset($jsonArray['email'])) {
            $result->email = $jsonArray['email'];
        }
        if (! isset($jsonArray['firstTimeLogin'])) {
            throw new \InvalidArgumentException('Missing the required parameter firstTimeLogin when calling SocialLoginResponseBody');
        }
        if (isset($jsonArray['firstTimeLogin'])) {
            $result->firstTimeLogin = $jsonArray['firstTimeLogin'];
        }
        if (! isset($jsonArray['apiToken'])) {
            throw new \InvalidArgumentException('Missing the required parameter apiToken when calling SocialLoginResponseBody');
        }
        if (isset($jsonArray['apiToken'])) {
            $result->apiToken = $jsonArray['apiToken'];
        }

        return $result;
    }
}
