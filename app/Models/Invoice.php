<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    public $incrementing = false; 
    protected $keyType = 'string'; 
    protected $fillable = [
        'id_invoice',
        'invoice_url',
        'description',
        'user_id' ,
        'branch_id',
       ' expiry_date',
        'invoice_number',
        'amount',
       'created_at',
        'updated_at',
       ' status',
    ];

    protected $table ='invoice';

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
