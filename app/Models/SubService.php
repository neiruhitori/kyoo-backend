<?php

namespace App\Models;

use App\Service;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SubService extends Model
{
    use HasFactory;
    protected  $table = 'sub_services';

    protected $fillable = [
        'branch_id',
        'name',
        'created_at',
        'updated_at',
    ];

    public function services()
    {
        return $this->belongsToMany(Service::class, 'service_sub_service')
                        ->withPivot('created_at', 'updated_at');
    }
}
