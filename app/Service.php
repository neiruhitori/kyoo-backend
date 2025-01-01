<?php

namespace App;

use App\Models\SubService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Service extends Model
{
    use HasFactory;

    protected $fillable = ['branch_id', 'name', 'department_id', 'prefix_queue', 'sla_duration', 'is_show', 'service_category_id', 'is_show_webkiosk'];

    public function Slot()
    {
        return $this->hasMany('App\Slot')->orderByRaw(
                        "CASE WHEN Day = 'sunday' THEN 1
                            WHEN Day = 'monday' THEN 2
                            WHEN Day = 'tuesday' THEN 3
                            WHEN Day = 'wednesday' THEN 4
                            WHEN Day = 'thursday' THEN 5
                            WHEN Day = 'friday' THEN 6
                            WHEN Day = 'saturday' THEN 7 END ASC"
                      )->orderBy('start_time');
    }

    public function Branch()
    {
        return $this->belongsTo('App\Branch')->withTrashed();
    }

    public function Department()
    {
        return $this->belongsTo('App\Department');
    }

    public function ScopeServiceCategory($query, $queue_type, $service_category_id)
    {
        return $query->when($queue_type == 'appointment', function($query) use ($service_category_id){
            $query->where('service_category_id', $service_category_id);
        });
    }

    /**
     * Get all of the WorkstationService for the Service
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function WorkstationService(): HasMany
    {
        return $this->hasMany(WorkstationService::class);
    }
    public function subServices()
    {
        return $this->belongsToMany(SubService::class, 'service_sub_service')
                    ->withPivot('created_at', 'updated_at','id');
    }
}
