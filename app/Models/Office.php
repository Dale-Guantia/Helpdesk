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

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'office_id');
    }

    public function problemCategories()
    {
        return $this->hasMany(ProblemCategory::class, 'office_id');
    }

    public function divisionHead()
    {
        // This assumes the users table has an `office_id` and `role` column.
        return $this->hasOne(User::class, 'office_id')->where('role', User::ROLE_DIVISION_HEAD);
    }
}
