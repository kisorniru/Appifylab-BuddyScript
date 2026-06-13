<?php

namespace App\Helpers;

use Carbon\Carbon;

class DateFormat
{
    public static function formatDbColumn($column, $format = 'Mon dd, YYYY')
    {
        $zone = self::getTimeZone();

        return "TO_CHAR($column AT TIME ZONE 'UTC' AT TIME ZONE '{$zone}',
      '{$format}'
    )";
    }

    public static function formatDate($column, $format = 'M d, Y')
    {
        $zone = self::getTimeZone();

        if (! ($column instanceof Carbon)) {
            $column = Carbon::parse($column);
        }

        return $column->setTimezone($zone)->format($format);
    }

    public static function getTimeZone()
    {
        return session('timezone') ?: config('app.timezone');
    }
}
