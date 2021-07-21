<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable, SoftDeletes, HasApiTokens;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'username', 'email', 'password', 'is_password_changed', 'role', 'token_external', 'platform', 'phone', 'branch_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

    public function Branch()
    {
        return $this->belongsTo('App\Branch')->withTrashed();
    }

    public function Customer()
    {
        return $this->hasOne('App\Customer');
    }

    public function Notification()
    {
        return $this->hasMany('App\Notification');
    }

    public function WorkstationVct()
    {
        return $this->hasOne('App\WorkstationVct', 'vct_id', 'id');
    }
}
