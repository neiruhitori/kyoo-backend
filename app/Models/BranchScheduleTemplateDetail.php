<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BranchScheduleTemplateDetail extends Model
{
    use HasFactory;

    protected $fillable = ['branch_id', 'schedule_template_id', 'schedule_template_detail_id', 'name', 'date'];
}
