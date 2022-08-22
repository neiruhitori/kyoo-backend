<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BranchToken extends Model
{    
    protected $fillable = ['branch_id', 'token'];

    /**
     * Get the Branch that owns the BranchToken
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function Branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
