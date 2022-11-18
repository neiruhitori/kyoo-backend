<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegencyLocation extends Model
{
    use HasFactory;

    protected $fillable = ['regency_id', 'lat', 'long'];

    public function Regency()
    {
        return $this->belongsTo('App\Models\Regency');
    }
}
