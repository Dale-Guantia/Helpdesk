<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class Ticket extends Model
{
    use HasFactory, Notifiable;
    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>s
     */
    protected $table = 'tickets';

    protected $fillable = [
        'user_id',
        'title',
        'reference_id',
        'description',
        'office_id',
        'priority_id',
        'status_id',
        'problem_category_id',
        'attachment',
        'guest_firstName',
        'guest_middleName',
        'guest_lastName',
    ];

    protected $casts = [
        'attachment' => 'array',
    ];


    protected static function booted()
    {
        static::creating(function ($ticket) {
            $today = now()->format('mdy'); // e.g., 052725
            $countToday = static::whereDate('created_at', now()->toDateString())->count();

            do {
                $countToday++;
                $increment = str_pad($countToday, 4, '0', STR_PAD_LEFT);
                $referenceId = "{$increment}-{$today}";
            } while (static::where('reference_id', $referenceId)->exists());

            $ticket->reference_id = $referenceId;
        });
    }


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
