<?php

namespace App\Supports;

class DateFormat {
    public static $DAYS = [
        'sunday' => 'minggu',
        'monday' => 'senin',
        'tuesday' => 'selasa',
        'wednesday' => 'rabu',
        'thursday' => 'kamis',
        'friday' => 'jum\'at',
        'saturday' => 'sabtu'
    ];

    public static function secondsToTime($seconds)
    {
        $s = round($seconds);

        return sprintf(
            '%02d:%02d:%02d',
            ($s / 3600),
            ($s / 60 % 60),
            $s % 60
        );
    }

    public static function daysLocale($day)
    {
        return DateFormat::$DAYS[$day];
    }
}