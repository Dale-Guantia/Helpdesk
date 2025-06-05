<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\HasAvatar;
use Illuminate\Support\Facades\Storage;
use Filament\Panel;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable implements FilamentUser, HasAvatar //MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    const ROLE_SUPER_ADMIN = 1;
    const ROLE_HRDO_DIVISION_HEAD = 2;
    const ROLE_HRDO_STAFF = 3;
    const ROLE_EMPLOYEE = 4;
    const DEFAULT_ROLE = self::ROLE_EMPLOYEE;
    const ROLES = [
        self::ROLE_SUPER_ADMIN => 'Super Admin',
        self::ROLE_HRDO_DIVISION_HEAD => 'HRDO Division Head',
        self::ROLE_HRDO_STAFF => 'HRDO Staff',
        self::ROLE_EMPLOYEE => 'Employee',
    ];

    public function isSuperAdmin()
    {
        return $this->role === self::ROLE_SUPER_ADMIN;
    }

    public function isHrdoDivisionHead()
    {
        return $this->role === self::ROLE_HRDO_DIVISION_HEAD;
    }

    public function isHrdoStaff()
    {
        return $this->role === self::ROLE_HRDO_STAFF;
    }

    public function isEmployee()
    {
        return $this->role === self::ROLE_EMPLOYEE;
    }

    public function isAgent(): bool
    {
        return in_array($this->role, [
            self::ROLE_SUPER_ADMIN,
            self::ROLE_HRDO_DIVISION_HEAD,
            self::ROLE_HRDO_STAFF,
        ]);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->isSuperAdmin() || $this->isHrdoDivisionHead() || $this->isHrdoStaff() || $this->isEmployee() || $this->is_active === 0;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'department_id',
        'office_id',
        'is_active',
        'role',
        'resolved_tickets_count',
        'avatar_url',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function office()
    {
        return $this->belongsTo(Office::class);
    }

    public function getFilamentAvatarUrl(): ?string
    {
        $avatarColumn = config('filament-edit-profile.avatar_column', 'avatar_url');
        return $this->$avatarColumn ? Storage::url($this->$avatarColumn) : null;
    }
}
