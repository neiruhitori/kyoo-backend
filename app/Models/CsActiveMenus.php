<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CsActiveMenus extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'cs_active_menus';

    protected $fillable = [
        'feature_id',
        'branch_id',
    ];

    /**
     * @return BelongsTo
     */
    public function Feature()
    {
        return $this->belongsTo(MenuFeatures::class);
    }
}
