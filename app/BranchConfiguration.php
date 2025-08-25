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
    'serving_directly',
    'whatsapp_type',
    'api_wa',
    'api_token',
    'template_booking_form',
    'signage_vo_format',
    'vo_call_style',
    'web_style',
    'ticket_style',
    'cs_page',
    'notif_popup',
    'notif_sound',
    'sandbox_url',
    'is_live_test'
  ];
}
