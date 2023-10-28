<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebkioskToken extends Model
{
    use HasFactory;

    protected $table = 'webkiosk_tokens';

    protected $fillable = ['webkiosk_configuration_id', 'token'];
}
