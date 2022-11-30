<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DirectQueue extends Model
{
    protected $fillable = ['queue_no', 'email', 'user_id', 'vct_id', 'workstation_service_id', 'name', 'phone', 'direct_queue_channel', 'status', 'called_at', 'done_at', 'recall_count', 'requeue_count', 'rating', 'is_liked', 'service_id', 'workstation_id', 'booking_code', 'client_id', 'fcm_id', 'waiting_duration', 'serving_duration', 'branch_id'];

    protected $cast = [
        'waiting_duration' => 'integer',
        'serving_duration' => 'integer'
    ];

    public function getCreatedAtAttribute($value)
    {
        return date('Y-m-d H:i:s', \strtotime($value));
    }

    public function getUpdatedAtAttribute($value)
    {
        return date('Y-m-d H:i:s', \strtotime($value));
    }

    public function WorkstationService()
    {
        return $this->belongsTo('App\WorkstationService');
    }

    public function WorkstationServices()
    {
        return $this->hasMany('App\WorkstationService', 'workstation_id', 'workstation_id');
    }

    /**
     * Get the Service that owns the DirectQueue
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function Service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Get all of the WorkstationVct for the DirectQueue
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function WorkstationVct(): HasOne
    {
        return $this->hasOne(WorkstationVct::class, 'workstation_id', 'workstation_id');
    }

    public function Workstation(): BelongsTo
    {
        return $this->belongsTo(Workstation::class);
    }

    public function scopeWithoutCanceled($query)
    {
        $query->where('status', '!=', 'canceled');
    }

    public function isWaiting()
    {
        return $this->status == 'waiting';
    }

    public function isServed()
    {
        return in_array($this->status, ['served', 'end served']);
    }

    public function isNoShow()
    {
        return $this->status == 'no show';
    }

    public function isBranch($branchId)
    {
        return $this->branch_id && $this->branch_id == $branchId;
    }

    public function isService($serviceId)
    {
        return $this->service_id && $this->service_id == $serviceId;
    }
}
