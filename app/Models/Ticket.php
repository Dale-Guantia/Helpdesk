<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;
// use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class Ticket extends Model
{
    use HasFactory, Notifiable; //softDeletes;
    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>s
     */

    const STATUS_PENDING = 1;
    const STATUS_RESOLVED = 2;
    const STATUS_UNASSIGNED = 3;
    const STATUS_REOPENED = 4;
    const STATUSES = [
        self::STATUS_RESOLVED => 'Resolved',
        self::STATUS_PENDING => 'Pending',
        self::STATUS_UNASSIGNED => 'Unassigned',
        self::STATUS_REOPENED => 'Reopened',
    ];

    public function isResolved()
    {
        return $this->role === self::STATUS_PENDING;
    }

    public function isPending()
    {
        return $this->role === self::STATUS_RESOLVED;
    }

    public function isUnassigned()
    {
        return $this->role === self::STATUS_UNASSIGNED;
    }

    public function isReopened()
    {
        return $this->role === self::STATUS_REOPENED;
    }


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

            $creatorUserId = $ticket->user_id; // Get the ID of the user who created the ticket

            // Notify Super Admins
            // Fetch all Super Admins
            $superAdmins = User::where('role', User::ROLE_SUPER_ADMIN)->get();

            foreach ($superAdmins as $admin) {
                // Notify if the admin is NOT the creator of the ticket
                if ($admin->id !== $creatorUserId) {
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
                }
            }

            // Notify Division Heads of the relevant office/department for new unassigned tickets
            if ($ticket->office_id || $ticket->department_id) {
                $divisionHeads = User::where('role', User::ROLE_DIVISION_HEAD)
                    ->where(function ($query) use ($ticket) {
                        $query->when($ticket->office_id, fn($q) => $q->where('office_id', $ticket->office_id))
                            ->when(!$ticket->office_id && $ticket->department_id, fn($q) => $q->where('department_id', $ticket->department_id));
                    })->get();

                foreach ($divisionHeads as $divisionHead) {
                    // Notify if the Division Head is NOT the creator AND their division_id matches
                    // We already filtered by division_id in the query, so just check creator here.
                    if ($divisionHead->id !== $creatorUserId) {
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
            }
        });

        // --- NOTIFICATION LOGIC FOR UPDATED TICKETS ---
        static::updated(function ($ticket) {
            // Prevent double notification if it was just created (already handled by static::created)
            if ($ticket->wasRecentlyCreated) {
                return;
            }

            // Determine if the 'assigned_to_user_id' field specifically changed
            $assignmentChanged = $ticket->isDirty('assigned_to_user_id') && $ticket->assigned_to_user_id !== null;
            $oldAssignedToUserId = $ticket->getOriginal('assigned_to_user_id');

            // Load relationships needed for notification logic
            $ticket->load('user'); // Creator of the ticket
            $ticket->load('assignedToUser'); // Currently assigned user

            // --- Define a helper for common notification structure ---
            $sendTicketUpdateNotification = function (
                $recipient,
                string $title,
                string $body,
                ?string $icon = 'heroicon-o-information-circle',
                string $viewActionLabel = 'View Ticket'
            ) use ($ticket) {
                Notification::make()
                    ->title($title)
                    ->body($body)
                    ->icon($icon)
                    ->actions([
                        Action::make('view')
                            ->label($viewActionLabel)
                            ->url(route('filament.ticketing.resources.tickets.view', ['record' => $ticket->id]))
                            ->button()
                            ->openUrlInNewTab(false),
                    ])
                    ->sendToDatabase($recipient);
            };

            // Get the user who performed the update (assuming they are authenticated)
            $updater = auth()->user();

            if ($ticket->user) { // Ensure the ticket has a creator
                if ($ticket->user->id !== $updater->id) { // This is the key change: Notifying if updater is *NOT* the creator
                    // Now, check the role of the ticket creator
                    if ($ticket->user->isAgent() || $ticket->user->isEmployee()) {
                        $sendTicketUpdateNotification(
                            $ticket->user,
                            'Your Ticket Was Updated!',
                            "Your ticket #{$ticket->reference_id} has been updated or received a new reply.",
                            'heroicon-o-arrow-path',
                            'View Ticket',
                        );
                    }
                }
            }

            // 3. Notify Staff:
            // a. If a new ticket is assigned to them by a Division Head (assignmentChanged is true)
            // b. If there are other changes to an assigned ticket (and they are the assigned staff)
            if ($ticket->assignedToUser && $ticket->assignedToUser->isStaff()) {
                if ($assignmentChanged) {
                    // Notify the new assignee (staff) about the assignment
                    $sendTicketUpdateNotification(
                        $ticket->assignedToUser,
                        'New Ticket Assignment!',
                        "Ticket #{$ticket->reference_id} has been assigned to you.",
                        'heroicon-o-bell',
                        'View Assigned Ticket'
                    );

                    // If there was an old assignee, notify them it was re-assigned (optional, but good practice)
                    if ($oldAssignedToUserId && $oldAssignedToUserId !== $ticket->assigned_to_user_id) {
                         $oldAssignee = User::find($oldAssignedToUserId);
                         if ($oldAssignee && $oldAssignee->isStaff()) {
                             $sendTicketUpdateNotification(
                                 $oldAssignee,
                                 'Ticket Re-assigned!',
                                 "Ticket #{$ticket->reference_id} has been re-assigned from you.",
                                 'heroicon-o-x-circle',
                                 'View Ticket'
                             );
                         }
                    }

                } else {
                    // Notify existing assigned staff if the ticket changes and they are not the updater
                    // We only notify if the update was NOT performed by the assigned staff themselves,
                    // otherwise, they already know about their own changes.
                    if ($ticket->assignedToUser->id !== $updater?->id) {
                         $sendTicketUpdateNotification(
                             $ticket->assignedToUser,
                             'Assigned Ticket Updated!',
                             "The ticket #{$ticket->reference_id} assigned to you has been updated or received a new reply.",
                             'heroicon-o-information-circle'
                         );
                    }
                }
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

    public function problemCategory()
    {
        return $this->belongsTo(ProblemCategory::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
