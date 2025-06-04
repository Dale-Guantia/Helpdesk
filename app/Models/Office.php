<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class Office extends Model
{
    use HasFactory, Notifiable;
    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>s
     */

    protected $table = 'offices';

    protected $fillable = [
        'office_name',
        'department_id',
        'created_at',
        'updated_at',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
