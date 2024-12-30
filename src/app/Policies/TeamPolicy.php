<?php

namespace App\Policies;

use App\Models\Team;
use App\Models\User;

class TeamPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the user is the captain of the team.
     */
    public function isCaptain(User $user, Team $team)
    {
        return $team->captain_id === $user->id;
    }
}
