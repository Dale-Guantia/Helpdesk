<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class Ticket extends Model
{
    use HasFactory, Notifiable, SoftDeletes;
    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>s
     */
    protected $table = 'tickets';

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'office_id',
        'priority_id',
        'status_id',
        'problem_category_id',
        'attachment',
    ];

    protected $casts = [
        'attachment' => 'array',
    ];



    public function User()
    {
        return $this->belongsTo(User::class);
    }
    public function office()
    {
        return $this->belongsTo(Office::class);
    }

    public function priority()
    {
        return $this->belongsTo(Priority::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    public function problemCategory()
    {
        return $this->belongsTo(ProblemCategory::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
