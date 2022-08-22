<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RegistrationBranch extends Model
{
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
