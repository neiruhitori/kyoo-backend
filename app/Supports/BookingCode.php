<?php

namespace App\Supports;

class BookingCode {
    private $permittedChars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    public static function generate()
    {
        $randomString = '';

        for($i = 0; $i < 5; $i++) {
            $randomString .= (new self)->permittedChars[mt_rand(0, strlen((new self)->permittedChars) - 1)];
        }
    
        return $randomString;
    }
}