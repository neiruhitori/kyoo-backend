<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    use SoftDeletes;

    protected $fillable = ['industry_category_id', 'schedule_template_id', 'name', 'email', 'address', 'description', 'fixed_phone', 'mobile_phone', 'lat', 'long', 'country', 'regency_id', 'logo', 'photo', 'likes', 'is_active'];

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

    public function CS()
    {
        return $this->hasMany('App\User')->withTrashed()->where('role', 'cs');
    }

    public function Schedule()
    {
        return $this->hasMany('App\Schedule');
    }

    public function ScheduleTemplate()
    {
        return $this->belongsTo('App\ScheduleTemplate');
    }

    public function Service()
    {
        return $this->hasMany('App\Service');
    }
}
