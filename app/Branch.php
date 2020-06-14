<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    use SoftDeletes;

    protected $fillable = ['industry_category_id', 'schedule_template_id', 'name', 'email', 'address', 'description', 'fixed_phone', 'mobile_phone', 'lat', 'long', 'country', 'regency_id', 'logo', 'photo', 'likes', 'is_active', 'timezone'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'regency_id' => 'integer',
    ];

    public function IndustryCategory()
    {
        return $this->belongsTo('App\IndustryCategory')->withTrashed();
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
        return $this->hasMany('App\Schedule')->orderByRaw(
                        "CASE WHEN Day = 'sunday' THEN 1
                            WHEN Day = 'monday' THEN 2
                            WHEN Day = 'tuesday' THEN 3
                            WHEN Day = 'wednesday' THEN 4
                            WHEN Day = 'thursday' THEN 5
                            WHEN Day = 'friday' THEN 6
                            WHEN Day = 'saturday' THEN 7 END ASC"
                      );
    }

    public function ScheduleTemplate()
    {
        return $this->belongsTo('App\ScheduleTemplate')->withTrashed();
    }

    public function Service()
    {
        return $this->hasMany('App\Service');
    }
}
