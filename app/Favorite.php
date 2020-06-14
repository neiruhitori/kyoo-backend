<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    protected $fillable = ['user_id', 'branch_id'];

    public function Branch()
    {
        return $this->belongsTo('App\Branch')->withTrashed();
    }
}
