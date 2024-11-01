<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemPrices extends Model
{
    protected $table ='items_prices';

    protected $fillable = [
        'prices',
        'item_name',
        'created_at',
        'updated_at',
    ];

    use HasFactory;
}
