<?php

namespace App\Repositories;

use App\Models\Team;
use App\Models\Tournament;

class TournamentRepository extends Repository
{

    public function __construct(Tournament $model)
    {
        parent::__construct($model);
    }

    public function all()
    {
        return $this->model->with(['manager', 'participants'])->get();
    }

    public function find($id)
    {
        return $this->model->with(['manager', 'participants'])->find($id);
    }

    public function addParticipant(Tournament $tournament, $user_id)
    {
        $tournament->participants()->attach($user_id);
    }

    public function participateTeam(Tournament $tournament, Team $team)
    {
        $tournament->participants()->syncWithoutDetaching($team->members);
    }

}
