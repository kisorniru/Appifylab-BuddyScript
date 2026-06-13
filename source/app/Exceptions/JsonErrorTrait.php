<?php

namespace App\Exceptions;

use Illuminate\Http\Request;

trait JsonErrorTrait
{
    protected function toJsonResponse(Request $request, \Exception $ex, ErrorParameters $params)
    {
        $trace_string = explode("\n", $ex->getTraceAsString());

        $isDebug = env('APP_DEBUG', config('app.debug', false));
        $res = [
            'code' => $params->getCode(),
            'isSuccess' => $params->getIsSuccess(),
            'message' => ! empty($ex->getMessage()) ? $ex->getMessage() : $params->getMessage(),
        ];

        if ($isDebug) {
            $res['resultCode'] = $params->getResultCode();
            $res['debugMessage'] = $ex->getMessage();
            $res['debugTrace'] = $trace_string;
        }

        $headers = ['Content-type' => 'application/json; charset=utf-8'];

        return response()->json($res, $params->getHttpResponseCode(), $headers, JSON_PRETTY_PRINT);
    }
}
