<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SecretKeyAPi extends Model
{
    use HasFactory;

    protected $table ='user_secret_token';

    protected $fillable = [
        'user_id',
        'branch_id',
        'secret_token',
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo('App\User','user_id');
    }
    public function branch()
    {
        return $this->belongsTo('App\Branch','branch_id');
    }
}
