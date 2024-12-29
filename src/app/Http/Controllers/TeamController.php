<?php

namespace App\Http\Controllers;

use App\Http\Requests\TeamRequest;
use App\Http\Resources\TeamResource;
use App\Repositories\TeamRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TeamController extends Controller
{

    public function __construct(private readonly TeamRepository $team_repository){}

    public function index()
    {
        $teams = $this->team_repository->all();
        return TeamResource::collection($teams);
    }

    public function store(TeamRequest $request)
    {
        $team = $this->team_repository->create($request->validated());
        return new TeamResource($team);
    }

    public function show($id)
    {
        $team = $this->team_repository->find($id);

        if (!$team) {
            return response()->json(['message' => 'Team not found'], Response::HTTP_NOT_FOUND);
        }

        return new TeamResource($team);
    }

    public function update(TeamRequest $request, $id)
    {
        $team = $this->team_repository->update($id, $request->validated());

        if (!$team) {
            return response()->json(['message' => 'Team not found'], Response::HTTP_NOT_FOUND);
        }

        return new TeamResource($team);
    }

    public function destroy($id)
    {
        $deleted = $this->team_repository->delete($id);

        if (!$deleted) {
            return response()->json(['message' => 'Team not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json(['message' => 'Team deleted successfully']);
    }

}
