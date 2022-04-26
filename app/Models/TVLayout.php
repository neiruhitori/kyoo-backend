<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TVLayout extends Model
{
    use HasFactory;

    protected $table = 'tv_layouts';

    protected $guarded = ['id'];    

    public $timestamps = false;
}
