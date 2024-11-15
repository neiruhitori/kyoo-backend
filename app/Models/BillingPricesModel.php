<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillingPricesModel extends Model
{
    protected $guarded = [
        'id'
    ];

    protected $table ='billing_prices';

    public function Branches_type()
    {
        return $this->belongsTo('App\BranchType','branch_type_id');
    }

    use HasFactory;
}
