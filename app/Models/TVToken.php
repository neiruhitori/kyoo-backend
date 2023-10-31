<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TVToken extends Model
{
    use HasFactory;

    protected $table = 'tv_tokens';

    protected $fillable = ['tv_configuration_id', 'token'];
}
