<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ScheduleTemplateDetail extends Model
{
    protected $fillable = ['schedule_template_id', 'date', 'description'];
}
