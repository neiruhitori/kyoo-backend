<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BranchConfiguration extends Model
{
  protected $fillable = [
    'branch_id',
    'maximum_recall',
    'maximum_requeue_count',
    'allow_transfer',
    'queue_voice',
    'promotion',
    'queue_layout_configuration',
    'wa_notification',
    'wa_notification_owner',
    'phone_owner',
    'layer',
    'time_interval',
    'max_slots',
    'serving_directly'
  ];
}
