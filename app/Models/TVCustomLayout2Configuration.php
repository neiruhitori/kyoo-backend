<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TVCustomLayout2Configuration extends Model
{
    use HasFactory;

    protected $table = 'tv_custom_layout_2_configuration';
    protected $fillable = [
        'tv_configuration_id',
        'background_type',
        'background_image',
        'background_color',
        'datetime_color',
        'sidebar_subtitle_color',
        'waiting_list_card_color',
        'waiting_list_font_color',
        'calling_card_header_color',
        'calling_card_body_color',
        'calling_card_font_header_color',
        'font_queue_first_letter_color',
        'font_queue_color',
        'running_text',
        'running_text_color',
        'running_text_speed',
        'running_text_size',
        'logo_size',
        'text_time_size',
    ];
}
