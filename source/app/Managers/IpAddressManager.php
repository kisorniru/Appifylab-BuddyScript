<?php

namespace App\Managers;

use Illuminate\Http\Request;

class IpAddressManager
{
    public static function getIpAddress(): string
    {
        $request = request();

        $request->setTrustedProxies([$request->ip()], Request::HEADER_X_FORWARDED_FOR | Request::HEADER_X_FORWARDED_HOST | Request::HEADER_X_FORWARDED_PORT | Request::HEADER_X_FORWARDED_PROTO);

        $ipAddress = $request->ip();

        return $ipAddress;
    }
}
