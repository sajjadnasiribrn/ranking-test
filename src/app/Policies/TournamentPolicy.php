<?php

namespace App\Policies;

use App\Models\Tournament;
use App\Models\User;

class TournamentPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function isManager(User $user, Tournament $tournament)
    {
        return $tournament->manager_id === $user->id;
    }
}
