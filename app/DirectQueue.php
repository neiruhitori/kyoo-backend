<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class DirectQueue extends Model
{
    protected $fillable = ['queue_no', 'user_id', 'vct_id', 'workstation_service_id', 'name', 'phone', 'direct_queue_channel', 'status', 'called_at', 'done_at', 'recall_count', 'requeue_count', 'rating', 'is_liked', 'service_id'];

    public function getCreatedAtAttribute($value)
    {
        return date('Y-m-d H:m:s', \strtotime($value));
    }

    public function getUpdatedAtAttribute($value)
    {
        return date('Y-m-d H:m:s', \strtotime($value));
    }

    public function WorkstationService()
    {
        return $this->belongsTo('App\WorkstationService');
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
}
