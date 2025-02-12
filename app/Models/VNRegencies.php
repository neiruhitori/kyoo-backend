<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VNRegencies extends Model
{
    use HasFactory;

    protected $table = 'vnregion_regencies';

    protected $fillable = ['province_id', 'name'];

    public function province()
    {
        return $this->belongsTo(VNProvinces::class, 'province_id');
    }
}
