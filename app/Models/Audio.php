<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Audio extends Model
{
    use HasFactory;

    protected $table = 'audios';
    protected $fillable = ['filename', 'customer_name', 'file_size_in_bytes', 'duration', 'branch_id', 'workstation_id'];
}
