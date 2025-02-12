<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SGRegencies extends Model
{
    use HasFactory;
    protected $table = 'sgregion_regencies';

    protected $fillable = ['province_id', 'name'];

    public function province()
    {
        return $this->belongsTo(SGProvince::class, 'province_id');
    }
}
