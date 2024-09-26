<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{ 
    protected $guarded = [
        'id'
    ];

    protected $table ='subscription';

    public function user()
    {
        return $this->belongsTo('App\User','user_id');
    }
    public function branch()
    {
        return $this->belongsTo('App\Branch','branch_id');
    }

    use HasFactory;
}
