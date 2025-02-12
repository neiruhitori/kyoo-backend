<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VNProvinces extends Model
{
    use HasFactory;
    protected $table = 'vnregion_provincies';
    protected $fillable = ['name'];
}
