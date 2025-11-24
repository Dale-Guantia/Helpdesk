<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class ProblemCategory extends Model
{
    use HasFactory, Notifiable;
    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>s
     */
    protected $table = 'problem_categories';

    protected $fillable = [
        'department_id',
        'office_id',
        'category_name',
        'icon',
        'created_at',
        'updated_at',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function office()
    {
        return $this->belongsTo(Office::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'problem_category_id');
    }
}
