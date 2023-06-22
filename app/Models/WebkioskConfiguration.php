<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\WebkioskLayout;
use App\Models\WebkioskLayout2Configuration;

class WebkioskConfiguration extends Model
{
    protected $table = 'webkiosk_configuration';

    protected $fillable = ['branch_id', 'layout_id'];

    public function layout()
    {
        return $this->belongsTo(WebkioskLayout::class);
    }

    public function layoutConfiguration()
    {
        return $this->hasOne(WebkioskLayout2Configuration::class, 'webkios_configuration_id', 'id');
    }

}