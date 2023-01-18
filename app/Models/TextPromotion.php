<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TextPromotion extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'branch_id', 'text', 'color', 'font_size'];
}
