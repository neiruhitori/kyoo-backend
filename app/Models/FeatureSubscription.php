<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeatureSubscription extends Model
{
    use HasFactory;

    protected $fillable = ['branch_id', 'feature_id'];

    public function AdditionalFeature()
    {
        return $this->belongsTo('App\Models\AdditionalFeature', 'feature_id', 'id');
    }
}
