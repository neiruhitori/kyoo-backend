<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\WebkioskLayout;
use App\Models\WebkioskLayout2Configuration;

class WebkioskConfiguration extends Model
{
    protected $table = 'webkiosk_configuration';

    protected $fillable = ['branch_id', 'layout_id', 'active_menus'];

    const MENU_OPTIONS  = [
        ['label' => 'WA (WhatsApp)', 'value' => 'wa'],
        ['label' => 'Photo', 'value' => 'photo'],
        ['label' => 'Print', 'value' => 'print'],
    ];

    public function layout()
    {
        return $this->belongsTo(WebkioskLayout::class);
    }

    public function layoutConfiguration2()
    {
        return $this->hasOne(WebkioskLayout2Configuration::class, 'webkios_configuration_id', 'id');
    }

    public function layoutConfiguration3()
    {
        return $this->hasOne(WebkioskLayout3Configuration::class, 'webkios_configuration_id', 'id');
    }

    public function layoutConfiguration4()
    {
        return $this->hasOne(WebkioskLayout4Configuration::class, 'webkios_configuration_id', 'id');
    }

    public function WebkioskToken()
    {
        return $this->hasOne('App\Models\WebkioskToken');
    }

}
