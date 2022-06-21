<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdditionalFeature extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'name', 'created_at'];
}
