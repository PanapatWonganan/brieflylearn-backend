<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserGarden;

class UserGardenPolicy
{
    /**
     * Determine if the user can view the garden.
     */
    public function view(User $user, UserGarden $garden): bool
    {
        return $user->id === $garden->user_id;
    }

    /**
     * Determine if the user can update the garden.
     */
    public function update(User $user, UserGarden $garden): bool
    {
        return $user->id === $garden->user_id;
    }

    /**
     * Determine if the user can water the garden.
     */
    public function water(User $user, UserGarden $garden): bool
    {
        return $user->id === $garden->user_id;
    }
}
