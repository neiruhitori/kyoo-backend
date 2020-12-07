<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BranchConfiguration extends Model
{
    protected $fillable = ['branch_id', 'maximum_recall', 'maximum_requeue_count' , 'allow_transfer'];
}
