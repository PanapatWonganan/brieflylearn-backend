<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Enrollment;

class EnrollmentPolicy
{
    /**
     * Determine if the user can view the enrollment.
     */
    public function view(User $user, Enrollment $enrollment): bool
    {
        return $user->id === $enrollment->user_id;
    }

    /**
     * Determine if the user can update the enrollment.
     */
    public function update(User $user, Enrollment $enrollment): bool
    {
        return $user->id === $enrollment->user_id;
    }

    /**
     * Determine if the user can delete the enrollment.
     */
    public function delete(User $user, Enrollment $enrollment): bool
    {
        return $user->id === $enrollment->user_id;
    }
}
