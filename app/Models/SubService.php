<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Service;

class SubService extends Model
{
    use HasFactory;

    protected $fillable = ['parent_id', 'name'];

    public function ParentService()
    {
        $this->belongsTo(Service::class, 'parent_id', 'id');
    }
}
