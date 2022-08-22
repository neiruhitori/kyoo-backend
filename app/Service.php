<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    protected $fillable = ['branch_id', 'name', 'department_id'];

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
                      );
    }

    public function Branch()
    {
        return $this->belongsTo('App\Branch')->withTrashed();
    }

    public function Department()
    {
        return $this->belongsTo('App\Department');
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
}
