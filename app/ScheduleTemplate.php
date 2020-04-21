<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class ScheduleTemplate extends Model
{
    use SoftDeletes;
    
    protected $fillable = ['name', 'file'];

    public function ScheduleTemplateDetail()
    {
        return $this->hasMany('App\ScheduleTemplateDetail');
    }
}
