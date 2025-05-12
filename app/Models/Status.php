<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class Status extends Model
{
    use HasFactory, Notifiable, SoftDeletes;
    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>s
     */
    protected $table = 'statuses';

    protected $fillable = [
        'status_name',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'status_id');
    }
}
