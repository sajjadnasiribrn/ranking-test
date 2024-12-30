<?php

namespace App\Repositories;

use App\Models\Team;
use App\Models\User;

class TeamRepository extends Repository
{

    public function __construct(Team $model)
    {
        parent::__construct($model);
    }

    public function all()
    {
        return $this->model->with(['captain', 'members'])->get();
    }

    public function find($id)
    {
        return $this->model->with(['captain', 'members'])->find($id);
    }

    public function create($data)
    {
        $team = $this->model->create($data);
        $team->members()->attach([
            $data['captain_id'] => ['is_captain' => true]
        ]);
        return $team;
    }

    public function addMember(Team $team, $user_id)
    {
        $team->members()->attach($user_id);
    }

    public function removeMember(Team $team, $user_id)
    {
        $team->members()->detach($user_id);
    }

    public function isMemberOf(Team $team, $user_id)
    {
        return $team->members()->where('users.id', $user_id)->exists();
    }

}
