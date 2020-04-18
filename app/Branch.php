<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    use SoftDeletes;
    protected $fillable = ['industry_category_id', 'schedule_template_id', 'name', 'email', 'address', 'description', 'fixed_phone', 'mobile_phone', 'lat', 'long', 'country', 'regency_id', 'logo', 'photo', 'likes', 'is_validate', 'is_active'];
}
