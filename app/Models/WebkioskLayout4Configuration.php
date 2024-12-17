<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebkioskLayout4Configuration extends Model
{
    protected $table = 'webkiosk_layout_4_configuration';

    protected $fillable = [
        'webkios_configuration_id',
        'primary_background_type',
        'primary_background_image',
        'primary_background_color',
        'button_background_color',
        'botton_border_color',
        'font_color',
        'button_checkin_background_color',
        'button_checkin_border_color',
        'font_checkin_color',
        'logo',
        'logo_size',
        'ticket_logo',
    ];
}
