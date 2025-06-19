<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    use HasFactory, Notifiable, softDeletes;
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
        'custom_problem_category',
        'attachment',
        'guest_firstName',
        'guest_middleName',
        'guest_lastName',
        'resolved_at',
    ];

    protected $casts = [
        'attachment' => 'array',
        'resolved_at' => 'datetime',
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


        static::created(function ($ticket) {
            $ticket->load('user');
            $recipients = User::where(function ($query) use ($ticket) {
                $query->where('role', User::ROLE_SUPER_ADMIN)
                    ->orWhere(function ($q) use ($ticket) {
                        $q->whereIn('role', [
                                User::ROLE_DIVISION_HEAD,
                                User::ROLE_STAFF,
                            ])
                            ->where(function ($inner) use ($ticket) {
                                $inner->when($ticket->office_id, function ($q) use ($ticket) {
                                    $q->where('office_id', $ticket->office_id);
                                })->when(!$ticket->office_id && $ticket->department_id, function ($q) use ($ticket) {
                                    $q->where('department_id', $ticket->department_id);
                                });
                            });
                        });
            })->get();

            foreach ($recipients as $recipient) {
                Notification::make()
                    ->title('New Ticket Created')
                    ->body("Ticket #{$ticket->reference_id} has been created.")
                    ->icon('heroicon-o-information-circle')
                    ->actions([
                        Action::make('view')
                            ->label('View Ticket')
                            ->url(route('filament.ticketing.resources.tickets.view', ['record' => $ticket->id]))
                            ->button()
                            ->openUrlInNewTab(false),
                    ])
                    ->sendToDatabase($recipient);
                    // ->broadcast($recipient);
            }
        });


        static::updated(function ($ticket) {
            if ($ticket->wasRecentlyCreated) {
                return; // Prevent double notification
            }

            $ticket->load('user');

            $recipients = User::where(function ($query) use ($ticket) {
                $query->where('role', User::ROLE_SUPER_ADMIN)
                    ->orWhere(function ($q) use ($ticket) {
                        $q->whereIn('role', [
                                User::ROLE_DIVISION_HEAD,
                                User::ROLE_STAFF,
                            ])
                            ->where(function ($inner) use ($ticket) {
                                $inner->when($ticket->office_id, function ($q) use ($ticket) {
                                    $q->where('office_id', $ticket->office_id);
                                })->when(!$ticket->office_id && $ticket->department_id, function ($q) use ($ticket) {
                                    $q->where('department_id', $ticket->department_id);
                                });
                            });
                        });
            })->get();

            foreach ($recipients as $recipient) {
                Notification::make()
                    ->title('Ticket Updated')
                    ->body("Ticket #{$ticket->reference_id} has been updated.")
                    ->icon('heroicon-o-information-circle')
                    ->actions([
                        Action::make('view')
                            ->label('View Ticket')
                            ->url(route('filament.ticketing.resources.tickets.view', ['record' => $ticket->id]))
                            ->button()
                            ->openUrlInNewTab(false),
                    ])
                    ->sendToDatabase($recipient);
                    // ->broadcast($recipient);
            }

            if ($ticket->user && $ticket->user->isEmployee()) {
                Notification::make()
                    ->title('Your Ticket Was Updated')
                    ->body("Ticket #{$ticket->reference_id} has been updated.")
                    ->icon('heroicon-o-information-circle')
                    ->actions([
                        Action::make('view')
                            ->label('View Update')
                            ->url(route('filament.ticketing.resources.tickets.view', ['record' => $ticket->id]))
                            ->button(),
                    ])
                    ->sendToDatabase($ticket->user);
                    // ->broadcast($ticket->user);
            }
        });
    }


    public function User()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
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
