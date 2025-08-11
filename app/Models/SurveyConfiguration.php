<?php

namespace App\Models;

use App\Branch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SurveyConfiguration extends Model
{
    use HasFactory;
    protected $fillable = [
        'branch_id',
        'type',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function questions()
    {
        return $this->hasMany(SurveyQuestions::class, 'survey_config_id');
    }
}
