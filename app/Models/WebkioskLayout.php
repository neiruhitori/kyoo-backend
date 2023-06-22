<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebkioskLayout extends Model
{
    use HasFactory;

    protected $table = 'webkiosk_layout';

    protected $guarded = ['id'];
}
