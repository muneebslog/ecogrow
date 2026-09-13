<?php

namespace App\Policies;

use App\Models\Plant;
use App\Models\User;

class PlantPolicy
{
    /**
     * Determine whether the user can view the plant.
     */
    public function view(User $user, Plant $plant): bool
    {
        return $user->id === $plant->user_id;
    }

    /**
     * Determine whether the user can update the plant.
     */
    public function update(User $user, Plant $plant): bool
    {
        return $user->id === $plant->user_id;
    }
}
