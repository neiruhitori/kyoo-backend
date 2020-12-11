<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DirectQueue extends Model
{
    protected $fillable = ['queue_no', 'user_id', 'vct_id', 'workstation_id', 'service_id', 'name', 'phone', 'direct_queue_channel', 'status', 'called_at', 'done_at', 'recall_count'];

    public function Service()
    {
        return $this->belongsTo('App\Service');
    }
}
