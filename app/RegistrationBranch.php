<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RegistrationBranch extends Model
{
    use SoftDeletes;
    
    protected $fillable = ['industry_category_id', 'name', 'email', 'password', 'country', 'phone', 'address', 'regency_id', 'is_email_verified', 'queue_type'];
    protected $hidden = ['password'];

    public function Regency()
    {
        return $this->belongsTo('App\Models\Regency');
    }

    public function IndustryCategory()
    {
        return $this->belongsTo('App\IndustryCategory')->withTrashed();
    }
}
