<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\CsActiveMenus;

class Branch extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'industry_category_id',
        'schedule_template_id',
        'name',
        'email',
        'address',
        'description',
        'fixed_phone',
        'mobile_phone',
        'lat',
        'long',
        'country',
        'regency_id',
        'logo',
        'photo',
        'likes',
        'is_active',
        'timezone',
        'branch_type_id',
        'max_counter',
        'corporate_id',
        'license_expiration_date'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'regency_id' => 'integer',
        'lat' => 'double',
        'long' => 'double'
    ];

    protected $appends = ['is_today_open'];

    public function scopeOnsite($query)
    {
        $query->whereHas('BranchType', function ($q) {
            $q->where('is_direct_queue', true);
        });
    }

    public function getQueueTypeAttribute()
    {
        if ($this->BranchType->is_direct_queue) {
            return 'onsite';
        }

        if ($this->BranchType->is_appointment) {
            return 'appointment';
        }

        if ($this->BranchType->is_exhibition) {
            return 'exhibition';
        }
    }

    public function getIsPremiumAttribute()
    {
        if ($this->BranchType) {
            return $this->BranchType->is_premium;
        }

        return false;
    }

    public function getIsTodayOpenAttribute()
    {
        $holiday = $this->Holiday->filter(function ($h) {
            return $h->date == date('Y-m-d');
        })->first();
        if ($holiday) {
            return false;
        }

        $closedDay = $this->Schedule->filter(function ($s) {
            return $s->day == strtolower(date('l')) &&
                $s->status == 'closed';
        })->first();
        if ($closedDay) {
            return false;
        }

        $afterHours = $this->Schedule->filter(function ($s) {
            return $s->day == strtolower(date('l')) &&
                $s->status == 'open' &&
                ($s->start_time > date('H:i:s') || $s->end_time < date('H:i:s'));
        })->first();

        if ($afterHours) {
            return false;
        }

        return true;
    }

    public function hasAccess($feature) {
        if (!$this->getIsPremiumAttribute()) {
            return false;
        }

        $branch = $this->FeatureSubscription->filter(function ($v) use ($feature) {
            return $v->AdditionalFeature->name == $feature;
        })->first();

        if ($branch) {
            return true;
        }

        return false;
    }

    public function getFeatures()
    {
        return $this->FeatureSubscription->map(function ($fs) {
            return $fs->AdditionalFeature->name;
        });
    }

    public function IndustryCategory()
    {
        return $this->belongsTo('App\IndustryCategory')->withTrashed();
    }

    public function FeatureSubscription()
    {
        return $this->hasMany('App\Models\FeatureSubscription');
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

    public function Holiday()
    {
        return $this->hasMany('App\Models\BranchScheduleTemplateDetail', 'branch_id', 'id');
    }

    public function Service()
    {
        return $this->hasMany('App\Service');
    }

    public function BranchType()
    {
        return $this->belongsTo('App\BranchType');
    }

    public function BranchConfiguration()
    {
        return $this->hasOne('App\BranchConfiguration');
    }

    public function Departments()
    {
        return $this->hasMany('App\Department');
    }

    public function Workstations()
    {
        return $this->hasManyThrough('App\Workstation', 'App\Department');
    }

    /**
     * Get the BranchToken associated with the Branch
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function BranchToken(): HasOne
    {
        return $this->hasOne(BranchToken::class);
    }

    public function Corporate()
    {
        return $this->belongsTo('App\Models\Corporate');
    }

    /**
     * @param $workstationID
     * @return array
     */
    public function getCsActiveMenus($workstationID): array
    {
        $csMenusFeature = CsActiveMenus::where('branch_id', $this->id)->with('feature')->get();
        $groupedData = [];

        foreach ($csMenusFeature as $item) {
            if (empty($item->feature)) {
                continue;
            }

            $groupLabel = $item->feature->group_label;
            if ($groupLabel === '-') {
                $groupedData[] = (object) [
                    'name' => $item->feature->name,
                    'code' => $item->feature->code,
                    'name_label' => $item->feature->name_label,
                    'route' => $this->generateRouteURL($workstationID, $item->feature->code, $item->feature->route),
                ];
                continue;
            }

            if (!isset($groupedData[$groupLabel])) {
                $groupedData[$groupLabel] = [];
            }

            $groupedData[$groupLabel][] = (object) [
                'name' => $item->feature->name,
                'code' => $item->feature->code,
                'name_label' => $item->feature->name_label,
                'route' => $this->generateRouteURL($workstationID, $item->feature->code, $item->feature->route),
            ];
        }

        return $groupedData;
    }

    private function generateRouteURL($workstationID, $code, $route) {
        if ($code == 'CT') {
            return str_replace('__branchID__', $workstationID, $route);
        }

        return $route;
    }
}
