<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use App\Models\Ticket;

class Department extends Model
{
    use HasFactory, Notifiable;
    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>s
     */

    protected $table = 'departments';

    protected $fillable = [
        'department_name',
        'created_at',
        'updated_at',
    ];

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'department_id');
    }

    public function office()
    {
        return $this->belongsTo(Office::class);
    }
}
