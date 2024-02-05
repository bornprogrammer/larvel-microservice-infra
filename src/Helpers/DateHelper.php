<?php

namespace Laravel\Infrastructure\Helpers;

use Carbon\Carbon;

class DateHelper
{
    public static function convertDateStringToISO(string $dateString, string $format = "Y-m-d\TH:i:s"): string
    {
        if ($dateString) {
            return date($format, strtotime($dateString));
        }
        return "";
    }

    public static function fromDateString(string $dateString, string $fromFormat = "d/m/Y", string $format = "Y-m-d"): string
    {
        return Carbon::createFromFormat($fromFormat, $dateString)->format($format);
    }

    public static function convertMysqlDateTimeToReadable(string $mysqlDateTimeString, string $format = "d/m/Y"): string
    {
        return self::convertDateStringToISO($mysqlDateTimeString, $format);
    }

    public static function getGMTNow(string $format = "Y-m-d\TH:i:s"): string
    {
        $date = Carbon::now("GMT");
        return $date->format($format);
    }

    public static function getYearFromDate(string $dateString): string
    {
        return Carbon::createFromFormat('Y-m-d', $dateString)->format('Y');
    }

    public static function diffForHumans(string $timeStamp): string
    {
        return Carbon::createFromTimestamp($timeStamp)->diffForHumans();
    }
}
