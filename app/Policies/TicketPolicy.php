<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TicketPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isDivisionHead() || $user->isStaff() || $user->isEmployee();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Ticket $ticket): bool
    {
        if ($user && $user->isSuperAdmin()) {
            return true;
        }

        if ($user->isEmployee()) { // Check for employee role or no assigned roles
            return $ticket->user_id === $user->id; // Only creator can edit
        }

        $departmentAndDivisionMatch = (
            $ticket->department_id === $user->department_id &&
            $ticket->office_id === $user->office_id
        );

        // Division Head: Can edit if the department_id and office_id match.
        if ($user->isDivisionHead()) {
            return $departmentAndDivisionMatch;
        }

        // HRDO Staff: Can edit if department_id and office_id match AND ticket was assigned to them.
        if ($user->isStaff()) { // Assuming 'isStaff()' covers HRDO Staff role
            return $departmentAndDivisionMatch && ($ticket->assigned_to_user_id === $user->id);
        }

        // By default, if resolved and not an admin/staff, cannot edit
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isDivisionHead() || $user->isStaff() || $user->isEmployee();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Ticket $ticket): bool
    {
       if ($user && $user->isSuperAdmin()) {
            return true;
        }

        // Return false/not allow editing if the ticket is resolved (status_id === 2)
        if ($ticket->status_id === 2) {
            return false;
        }

        if ($user->isEmployee()) { // Check for employee role or no assigned roles
            return $ticket->user_id === $user->id; // Only creator can edit
        }

        $departmentAndDivisionMatch = (
            $ticket->department_id === $user->department_id &&
            $ticket->office_id === $user->office_id
        );

        // Division Head: Can edit if the department_id and office_id match.
        if ($user->isDivisionHead()) {
            return $departmentAndDivisionMatch;
        }

        // HRDO Staff: Can edit if department_id and office_id match AND ticket was assigned to them.
        if ($user->isStaff()) { // Assuming 'isStaff()' covers HRDO Staff role
            return $departmentAndDivisionMatch && ($ticket->assigned_to_user_id === $user->id);
        }

        // By default, if resolved and not an admin/staff, cannot edit
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Ticket $ticket): bool
    {
        return $user->isSuperAdmin() || $user->isDivisionHead();
    }

    /**
     * Determine whether the user can bulk delete the models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Ticket $ticket): bool
    {
        return $user->isSuperAdmin();
    }


    /**
     * Determine whether the user can restore the model.
     */
    public function restoreAny(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Ticket $ticket): bool
    {
        return $user->isSuperAdmin();
    }

        /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->isSuperAdmin();
    }
}
