<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    use SoftDeletes;

    protected $fillable = ['industry_category_id', 'schedule_template_id', 'name', 'email', 'address', 'description', 'fixed_phone', 'mobile_phone', 'lat', 'long', 'country', 'regency_id', 'logo', 'photo', 'likes', 'status', 'is_active'];

    public function IndustryCategory()
    {
        return $this->belongsTo('App\IndustryCategory');
    }

    public function Regency()
    {
        return $this->belongsTo('App\Models\Regency');
    }

    public function Admin()
    {
        return $this->hasMany('App\User')->where('role', 'admin_branch');
    }
}
