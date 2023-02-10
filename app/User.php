<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Passport\HasApiTokens;
use App\Notifications\ResetPasswordNotification;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'username', 'email', 'email_verified_at', 'password', 'is_password_changed', 'role', 'token_external', 'platform', 'phone', 'branch_id', 'last_login', 'token_personal', 'corporate_id'
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

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

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

    public function Corporate()
    {
        return $this->belongsTo('App\Models\Corporate');
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
