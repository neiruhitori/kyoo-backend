<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    use HasFactory;

    protected $fillable = ['type', 'title', 'branch_id', 'text', 'color', 'font_size', 'image_url', 'caption'];

    const TEXT_TYPE = 'text';
    const IMAGE_TYPE = 'image';
}
