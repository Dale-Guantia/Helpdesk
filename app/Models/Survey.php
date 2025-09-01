<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    use HasFactory;

    protected $table = 'surveys';

    protected $fillable = [
        'user_id',
        'problem_category_id',
        'submission_date',
        'responsiveness_rating',
        'timeliness_rating',
        'communication_rating',
        'suggestions',
    ];

    public function staff()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function service()
    {
        return $this->belongsTo(ProblemCategory::class, 'problem_category_id');
    }
}
