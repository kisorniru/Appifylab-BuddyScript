<?php

namespace App\Http\Responses;

class LoginResponseBody
{
    public $id;

    public $name;

    public $email;

    public $firstTimeLogin;

    public $apiToken;

    public static function fromJSON(string $json): LoginResponseBody
    {
        return self::fromArray(json_decode($json, true));
    }

    public static function fromArray(array $jsonArray): LoginResponseBody
    {
        $result = new LoginResponseBody;
        if (! isset($jsonArray['id'])) {
            throw new \InvalidArgumentException('Missing the required parameter id when calling LoginResponseBody');
        }
        if (isset($jsonArray['id'])) {
            $result->id = $jsonArray['id'];
        }
        if (! isset($jsonArray['name'])) {
            throw new \InvalidArgumentException('Missing the required parameter name when calling LoginResponseBody');
        }
        if (isset($jsonArray['name'])) {
            $result->name = $jsonArray['name'];
        }
        if (! isset($jsonArray['email'])) {
            throw new \InvalidArgumentException('Missing the required parameter email when calling LoginResponseBody');
        }
        if (isset($jsonArray['email'])) {
            $result->email = $jsonArray['email'];
        }
        if (! isset($jsonArray['firstTimeLogin'])) {
            throw new \InvalidArgumentException('Missing the required parameter firstTimeLogin when calling LoginResponseBody');
        }
        if (isset($jsonArray['firstTimeLogin'])) {
            $result->firstTimeLogin = $jsonArray['firstTimeLogin'];
        }
        if (! isset($jsonArray['apiToken'])) {
            throw new \InvalidArgumentException('Missing the required parameter apiToken when calling LoginResponseBody');
        }
        if (isset($jsonArray['apiToken'])) {
            $result->apiToken = $jsonArray['apiToken'];
        }

        return $result;
    }
}
