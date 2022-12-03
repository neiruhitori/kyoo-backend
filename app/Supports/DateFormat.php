<?php

namespace App\Supports;

class DateFormat {
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
}