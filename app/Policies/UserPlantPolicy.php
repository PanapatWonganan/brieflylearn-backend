<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserPlant;

class UserPlantPolicy
{
    /**
     * Determine if the user can view the plant.
     */
    public function view(User $user, UserPlant $plant): bool
    {
        return $user->id === $plant->user_id;
    }

    /**
     * Determine if the user can water the plant.
     */
    public function water(User $user, UserPlant $plant): bool
    {
        return $user->id === $plant->user_id;
    }

    /**
     * Determine if the user can harvest the plant.
     */
    public function harvest(User $user, UserPlant $plant): bool
    {
        return $user->id === $plant->user_id;
    }

    /**
     * Determine if the user can update the plant.
     */
    public function update(User $user, UserPlant $plant): bool
    {
        return $user->id === $plant->user_id;
    }

    /**
     * Determine if the user can delete the plant.
     */
    public function delete(User $user, UserPlant $plant): bool
    {
        return $user->id === $plant->user_id;
    }
}
