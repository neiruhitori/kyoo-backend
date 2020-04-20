<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RegistrationBranch extends Model
{
    protected $fillable = ['industry_category_id', 'name', 'email', 'password', 'country', 'phone', 'regency_id', 'is_email_verified'];

    public function Regency()
    {
        return $this->belongsTo('App\Model\Regency');
    }
}
