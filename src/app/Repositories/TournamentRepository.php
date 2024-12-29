<?php

namespace App\Repositories;

use App\Models\Tournament;

class TournamentRepository extends Repository
{

    public function __construct(Tournament $model)
    {
        parent::__construct($model);
    }

    public function all()
    {
        return $this->model->with(['manager', 'participants'])->all();
    }

    public function find($id)
    {
        return $this->model->with(['manager', 'participants'])->find($id);
    }

}
