<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistrationUser extends Model
{
    use HasFactory;

    protected $table = 'registration_user_mobile';
    protected $guarded = ['id'];
    protected $hidden = ['password'];
}
