<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\App;

class Controller extends BaseController
{
    private $language;

    public function __construct()
    {
        $this->language = request()->header('X-Language-Key');
        $locale = substr($this->language, 0, 2);
        App::setLocale($locale);
    }

    protected function getDefaultCacheTime(): int
    {
        return 0;
    }

    protected function jsonResponse(Request $request, $data, int $cacheTime, int $statusCode = 200, $newApiToken = null)
    {
        $headers = [];
        if ($newApiToken !== null) {
            $headers['Authorization'] = $newApiToken;
        } else {
            $token = $request->header('Authorization');
            if ($token) {
                $headers['Authorization'] = $token;
            }
        }

        if ($data !== null) {
            $response = response()->json($data, $statusCode, $headers);

            if ($cacheTime > 0) {
                $response->setClientTtl($cacheTime);

            }
        } else {
            $response = response()->json([], $statusCode, $headers);
        }

        return $response;
    }

    protected function binaryResponse(Request $request, $data, int $cacheTime, int $statusCode = 200, $contentType = null, $newApiToken = null)
    {
        $headers = [];
        if ($newApiToken !== null) {
            $headers['Authorization'] = $newApiToken;
        } else {
            $token = $request->header('Authorization');
            if ($token) {
                $headers['Authorization'] = $token;
            }
        }

        if ($contentType !== null) {
            $headers['Content-type'] = $contentType;
        }

        if ($data !== null) {
            $response = response()->make($data, $statusCode, $headers);

            if ($cacheTime > 0) {
                $response->setClientTtl($cacheTime);

            }
        } else {
            $response = response()->make('', $statusCode, $headers);
        }

        return $response;
    }
}
