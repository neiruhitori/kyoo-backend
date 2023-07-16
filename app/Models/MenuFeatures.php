<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuFeatures extends Model
{
    use HasFactory;

    protected $table = 'menu_features';

    protected $guarded = ['id'];
}
