<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CounterActivity extends Model
{
    use HasFactory;

    protected $table = 'counter_activity';

    protected $fillable = ['date', 'workstation_id', 'operation_duration', 'last_login', 'vct_id'];
}
