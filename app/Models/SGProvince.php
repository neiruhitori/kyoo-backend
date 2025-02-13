<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SGProvince extends Model
{
    use HasFactory;

    protected $table = 'sgregion_provinces';
    public $timestamps = false;
    protected $fillable = ['name','timezone'];
}
