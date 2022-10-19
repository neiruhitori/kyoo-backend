<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TermCondition extends Model
{
    use HasFactory;

    protected  $table = 'terms_conditions';

    protected $fillable = [
        'branch_id',
        'body'
    ];

    public function Branch()
    {
        return $this->belongsTo('App\Branch');
    }
}
