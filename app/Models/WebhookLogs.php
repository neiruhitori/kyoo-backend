<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebhookLogs extends Model
{
    use HasFactory;

    protected $table = 'webhook_logs';
    protected $guarded = ['id'];

    protected $casts = [
        'payload' => 'array',
    ];

    public function queue(){
        return $this->hasOne(AppointmentOnsite::class, 'id','queue_id');
    }
}
