<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

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
        'resolved_by',
        'assigned_to_user_id',
    ];

    protected $casts = [
        'attachment' => 'array',
        'resolved_at' => 'datetime',
    ];

    protected static function booted()
    {
        // --- REFERENCE ID GENERATION ---
        static::creating(function ($ticket) {
            $today = now()->format('mdy'); // e.g., '062425'

            $maxAttempts = 5;
            $attempt = 0;

            do {
                $uniqueIdGenerated = false;
                try {
                    DB::beginTransaction();

                    $latestTicket = static::where('reference_id', 'LIKE', '%-' . $today)
                                          ->orderBy('reference_id', 'desc')
                                          ->lockForUpdate() // Acquire a write lock
                                          ->first();

                    $sequentialNumber = 1; // Default starting number for the day

                    if ($latestTicket) {
                        // Extract the sequential part from the latest reference_id
                        // Example: '0003-062425' -> '0003'
                        $parts = explode('-', $latestTicket->reference_id);
                        if (count($parts) > 1 && is_numeric($parts[0])) { // Basic validation
                            $lastSequentialPart = (int) $parts[0];
                            $sequentialNumber = $lastSequentialPart + 1;
                        }
                    }

                    // Format the sequential number with leading zeros (e.g., 0001, 0002)
                    $increment = str_pad($sequentialNumber, 4, '0', STR_PAD_LEFT);
                    $referenceId = "{$increment}-{$today}";

                    if (static::where('reference_id', $referenceId)->exists()) {
                        DB::rollBack();
                        $attempt++;
                        \Log::warning("Generated reference ID '$referenceId' already exists, retrying generation (attempt: $attempt).");
                        usleep(100000); // Wait 100ms before retrying
                        continue; // Skip to next iteration of do-while
                    }

                    $ticket->reference_id = $referenceId;
                    DB::commit(); // Commit the transaction
                    $uniqueIdGenerated = true;

                } catch (QueryException $e) {
                    DB::rollBack(); // Rollback on any database error
                    // Specifically check for the duplicate entry error (SQLSTATE 23000, MySQL code 1062)
                    if ($e->getCode() === '23000' && str_contains($e->getMessage(), 'Duplicate entry')) {
                        $attempt++;
                        \Log::warning("Duplicate entry error during reference ID generation, retrying (attempt: $attempt). Error: " . $e->getMessage());
                        usleep(100000); // Wait 100ms before retrying
                        continue; // Try again
                    }
                    // Re-throw if it's another type of database error
                    throw $e;
                } catch (\Exception $e) {
                    DB::rollBack(); // Rollback on any other error
                    throw $e; // Re-throw general exceptions
                }

            } while (!$uniqueIdGenerated && $attempt < $maxAttempts);

            if (!$uniqueIdGenerated) {
                // If we've exhausted all attempts, throw an error
                throw new \RuntimeException("Failed to generate a unique reference ID for ticket after $maxAttempts attempts.");
            }
        });


        // --- NOTIFICATION LOGIC FOR CREATED TICKETS ---
        static::created(function ($ticket) {
            $ticket->load('user'); // Load the creator of the ticket

            // Notify Super Admins
            $superAdmins = User::where('role', User::ROLE_SUPER_ADMIN)->get();
            foreach ($superAdmins as $admin) {
                Notification::make()
                    ->title('New Ticket Created!')
                    ->body("Ticket #{$ticket->reference_id} has been created.")
                    ->icon('heroicon-o-information-circle')
                    ->actions([
                        Action::make('view')
                            ->label('View Ticket')
                            ->url(route('filament.ticketing.resources.tickets.view', ['record' => $ticket->id]))
                            ->button()
                            ->openUrlInNewTab(false),
                    ])
                    ->sendToDatabase($admin);
                    // ->broadcast($recipient);
            }

            // Notify Division Heads of the relevant office/department for new unassigned tickets
            // This assumes initial tickets become visible to DHs for assignment
            if ($ticket->office_id || $ticket->department_id) {
                $divisionHeads = User::where('role', User::ROLE_DIVISION_HEAD)
                    ->where(function ($query) use ($ticket) {
                        $query->when($ticket->office_id, fn($q) => $q->where('office_id', $ticket->office_id))
                              ->when(!$ticket->office_id && $ticket->department_id, fn($q) => $q->where('department_id', $ticket->department_id));
                    })->get();

                foreach ($divisionHeads as $divisionHead) {
                    Notification::make()
                        ->title('New Ticket Created!')
                        ->body("Ticket #{$ticket->reference_id} needs assignment in your division.")
                        ->icon('heroicon-o-information-circle')
                        ->actions([
                            Action::make('view')
                                ->label('View Ticket')
                                ->url(route('filament.ticketing.resources.tickets.view', ['record' => $ticket->id]))
                                ->button()
                                ->openUrlInNewTab(false),
                        ])
                        ->sendToDatabase($divisionHead);
                }
            }

            // Notify the creator (employee)
            if ($ticket->user && $ticket->user->isEmployee()) {
                Notification::make()
                    ->title('Your Ticket Was Created!')
                    ->body("Ticket #{$ticket->reference_id} has been successfully created.")
                    ->icon('heroicon-o-information-circle')
                    ->actions([
                        Action::make('view')
                            ->label('View Ticket')
                            ->url(route('filament.ticketing.resources.tickets.view', ['record' => $ticket->id]))
                            ->button(),
                    ])
                    ->sendToDatabase($ticket->user);
            }
        });

        // --- NOTIFICATION LOGIC FOR UPDATED TICKETS ---
        static::updated(function ($ticket) {
            // Check if 'assigned_to_user_id' was changed
            $wasAssigned = $ticket->getOriginal('assigned_to_user_id') === null && $ticket->assigned_to_user_id !== null;
            $assignmentChanged = $ticket->isDirty('assigned_to_user_id') && $ticket->assigned_to_user_id !== null;

            if ($ticket->wasRecentlyCreated) {
                return; // Prevent double notification from 'created'
            }

            $ticket->load('user'); // Load the creator
            $ticket->load('assignedToUser'); // Load the newly assigned user

            // Notify Super Admins about any update (they see everything)
            $superAdmins = User::where('role', User::ROLE_SUPER_ADMIN)->get();
            foreach ($superAdmins as $admin) {
                Notification::make()
                    ->title('Ticket Updated!')
                    ->body("Ticket #{$ticket->reference_id} has been updated.")
                    ->icon('heroicon-o-information-circle')
                    ->actions([
                        Action::make('view')
                            ->label('View Ticket')
                            ->url(route('filament.ticketing.resources.tickets.view', ['record' => $ticket->id]))
                            ->button()
                            ->openUrlInNewTab(false),
                    ])
                    ->sendToDatabase($admin);
                    // ->broadcast($recipient);
            }

            // Notify Division Heads of the relevant office/department about updates
            // (Only if it's in their division and not explicitly about an assignment to staff)
            if ($ticket->office_id || $ticket->department_id) {
                $divisionHeads = User::where('role', User::ROLE_DIVISION_HEAD)
                    ->where(function ($query) use ($ticket) {
                        $query->when($ticket->office_id, fn($q) => $q->where('office_id', $ticket->office_id))
                              ->when(!$ticket->office_id && $ticket->department_id, fn($q) => $q->where('department_id', $ticket->department_id));
                    })->get();

                foreach ($divisionHeads as $divisionHead) {
                    Notification::make()
                        ->title('Ticket Updated!')
                        ->body("Ticket #{$ticket->reference_id} in your division has been updated.")
                        ->icon('heroicon-o-information-circle')
                        ->actions([
                            Action::make('view')
                                ->label('View Ticket')
                                ->url(route('filament.ticketing.resources.tickets.view', ['record' => $ticket->id]))
                                ->button()
                                ->openUrlInNewTab(false),
                        ])
                        ->sendToDatabase($divisionHead);
                }
            }


            // Notify the newly assigned staff member
            if ($assignmentChanged) {
                if ($ticket->assignedToUser && $ticket->assignedToUser->isStaff()) {
                    Notification::make()
                        ->title('New Ticket Assignment!')
                        ->body("Ticket #{$ticket->reference_id} has been assigned to you.")
                        ->icon('heroicon-o-bell')
                        ->actions([
                            Action::make('view')
                                ->label('View Assigned Ticket')
                                ->url(route('filament.ticketing.resources.tickets.view', ['record' => $ticket->id]))
                                ->button(),
                        ])
                        ->sendToDatabase($ticket->assignedToUser);
                }
            }

            // Notify the creator (employee) about updates to their ticket
            if ($ticket->user && $ticket->user->isEmployee()) {
                Notification::make()
                    ->title('Your Ticket Was Updated!')
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

    public function assignedToUser() // <--- ADD THIS RELATIONSHIP
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
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
