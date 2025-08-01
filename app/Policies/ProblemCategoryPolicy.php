<?php

namespace App\Policies;

use App\Models\ProblemCategory;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProblemCategoryPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isDepartmentHead() || $user->isDivisionHead();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ProblemCategory $problemCategory): bool
    {
        return $user->isSuperAdmin() || $user->isDepartmentHead() || $user->isDivisionHead();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isDepartmentHead() || $user->isDivisionHead();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ProblemCategory $problemCategory): bool
    {
        return $user->isSuperAdmin() || $user->isDepartmentHead() || $user->isDivisionHead();
    }

   /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ProblemCategory $problemCategory): bool
    {
        return $user->isSuperAdmin()|| $user->isDepartmentHead() || $user->isDivisionHead();
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
    public function restore(User $user, ProblemCategory $problemCategory): bool
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
    public function forceDelete(User $user, ProblemCategory $problemCategory): bool
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
