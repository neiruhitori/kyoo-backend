<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Corporate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'email', 'address', 'mobile_phone',
        'country', 'regency_id', 'lat', 'long',
        'logo', 'is_active', 'timezone'
    ];

    public function scopeActive($query)
    {
        $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        $query->where('is_active', false);
    }

    public function Regency()
    {
        return $this->belongsTo('App\Models\Regency');
    }
}
