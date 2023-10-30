<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TVConfiguration extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $table = 'tv_configurations';

    public function TVToken()
    {
        return $this->hasOne('App\Models\TVToken', 'tv_configuration_id', 'id');
    }
}
