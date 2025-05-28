<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class Comment extends Model
{
    use HasFactory, Notifiable;
    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>s
     */

    protected $table = 'comments';

    protected $fillable = [
        'comment',
        'attachment',
        'user_id',
        'ticket_id',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'attachment' => 'array',
    ];

    protected static function booted()
    {
        static::created(function ($comment) {
            $comment->ticket?->touch();
        });
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function ticket() {
        return $this->belongsTo(Ticket::class);
    }
}
