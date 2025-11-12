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
use Illuminate\Support\Facades\File;
// use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable implements FilamentUser, HasAvatar //MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable; //SoftDeletes;

    const ROLE_SUPER_ADMIN = 1;
    const ROLE_DIVISION_HEAD = 2;
    const ROLE_STAFF = 3;
    const ROLE_EMPLOYEE = 4;
    const ROLE_DEPT_HEAD = 5;
    const DEFAULT_ROLE = self::ROLE_EMPLOYEE;
    const ROLES = [
        self::ROLE_SUPER_ADMIN => 'Super Admin',
        self::ROLE_DEPT_HEAD => 'Department Head',
        self::ROLE_DIVISION_HEAD => 'Division Head',
        self::ROLE_STAFF => 'HRDO Staff',
        self::ROLE_EMPLOYEE => 'Guest',
    ];

    public function isSuperAdmin()
    {
        return $this->role === self::ROLE_SUPER_ADMIN;
    }

    public function isDepartmentHead()
    {
        return $this->role === self::ROLE_DEPT_HEAD;
    }

    public function isDivisionHead()
    {
        return $this->role === self::ROLE_DIVISION_HEAD;
    }

    public function isStaff()
    {
        return $this->role === self::ROLE_STAFF;
    }

    public function isEmployee()
    {
        return $this->role === self::ROLE_EMPLOYEE;
    }

    public function isAgent(): bool
    {
        return in_array($this->role, [
            self::ROLE_SUPER_ADMIN,
            self::ROLE_DEPT_HEAD,
            self::ROLE_DIVISION_HEAD,
            self::ROLE_STAFF,
        ]);
    }

    public function getRoleNameAttribute(): string
    {
        return self::ROLES[$this->role] ?? 'Unknown Role';
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->isSuperAdmin() || $this->isDepartmentHead() || $this->isDivisionHead() || $this->isStaff() || $this->isEmployee() || $this->is_active === 0;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */

    protected $fillable = [
        'name',
        'username',
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
        'custom_fields',
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
            'custom_fields' => 'array'
        ];
    }

    public function overdueTickets()
    {
        return $this->hasMany(Ticket::class, 'assigned_to_user_id')
            ->where(function ($query) {
                $query->where(function ($q) {
                    // Pending / Unassigned overdue
                    $q->whereIn('status_id', [Ticket::STATUS_PENDING, Ticket::STATUS_UNASSIGNED])
                    ->whereRaw('DATEDIFF(NOW(), created_at) >= 3');
                })
                ->orWhere(function ($q) {
                    // Reopened overdue
                    $q->where('status_id', Ticket::STATUS_REOPENED)
                    ->whereRaw('DATEDIFF(NOW(), reopened_at) >= 3');
                })
                ->orWhere(function ($q) {
                    // Resolved after 3 days (created -> resolved)
                    $q->where('status_id', Ticket::STATUS_RESOLVED)
                    ->whereRaw('DATEDIFF(resolved_at, created_at) > 3');
                })
                ->orWhere(function ($q) {
                    // Reopened → Resolved after 3 days (reopened -> resolved)
                    $q->where('status_id', Ticket::STATUS_RESOLVED)
                    ->whereNotNull('reopened_at')
                    ->whereRaw('DATEDIFF(resolved_at, reopened_at) >= 3');
                });
            });
    }

    public function getAvatarUrl()
    {
        if ($this->avatar_url && Storage::disk('public')->exists($this->avatar_url)) {
            return asset('storage/' . $this->avatar_url);
        }

        // Fallback to UI Avatars API
        $name_initials = urlencode($this->name);
        return "https://ui-avatars.com/api/?name={$name_initials}&background=random&color=fff&font-size=0.35&length=3";
    }

    public function getAvatarWebpUrl()
    {
        if ($this->avatar_url) {
            // Example: 'avatars/1.jpg' -> 'avatars/1.webp'
            $webpPath = pathinfo($this->avatar_url, PATHINFO_DIRNAME) . '/'
                        . pathinfo($this->avatar_url, PATHINFO_FILENAME)
                        . '.webp';

            // Only return a path if the WebP file actually exists in storage
            if (Storage::disk('public')->exists($webpPath)) {
                return asset('storage/' . $webpPath);
            }
        }

        // Return an empty string or null if the WebP path is not valid/found.
        return null;
    }

    public function getFilamentAvatarUrl(): ?string
    {
        $avatarColumn = config('filament-edit-profile.avatar_column', 'avatar_url');
        return $this->$avatarColumn ? Storage::url($this->$avatarColumn) : null;
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function office()
    {
        return $this->belongsTo(Office::class);
    }

    public function resolvedTickets()
    {
        return $this->hasMany(Ticket::class, 'resolved_by')->whereNotNull('resolved_at');
    }

    public function surveys()
    {
        return $this->hasMany(Survey::class);
    }
}
