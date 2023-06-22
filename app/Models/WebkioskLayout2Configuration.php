<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebkioskLayout2Configuration extends Model
{
    protected $table = 'webkiosk_layout_2_configuration';

    protected $fillable = [
        'webkios_configuration_id',
        'primary_background_type',
        'primary_background_image',
        'primary_background_color',
        'secondary_background_type',
        'secondary_background_image',
        'secondary_background_color',
        'button_background_color',
        'botton_border_color',
        'font_color',
    ];
}