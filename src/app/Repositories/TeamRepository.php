<?php

namespace App\Repositories;

use App\Models\Team;

class TeamRepository extends Repository
{

    public function __construct(Team $model)
    {
        parent::__construct($model);
    }

    public function all()
    {
        return $this->model->with(['captain', 'members'])->all();
    }

    public function find($id)
    {
        return $this->model->with(['captain', 'members'])->find($id);
    }

}
