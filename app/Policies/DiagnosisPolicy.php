<?php

namespace App\Policies;

use App\Models\Diagnosis;
use App\Models\User;

class DiagnosisPolicy
{
    /**
     * Determine whether the user can view the diagnosis.
     */
    public function view(User $user, Diagnosis $diagnosis): bool
    {
        return $user->id === $diagnosis->user_id;
    }
}
