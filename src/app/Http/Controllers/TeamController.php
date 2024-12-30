<?php

namespace App\Http\Controllers;

use App\Http\Requests\MemberRequest;
use App\Http\Requests\TeamRequest;
use App\Http\Resources\TeamResource;
use App\Models\Team;
use App\Repositories\TeamRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

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
        $data = $request->validated();
        $data['captain_id'] = Auth::id();

        $team = $this->team_repository->create($data);
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

    public function addMember(MemberRequest $request, Team $team)
    {
        $this->authorize('isCaptain', $team);

        $this->team_repository->addMember($team, $request->user_id);

        return response()->json(['message' => 'Member added successfully']);
    }

    public function removeMember(MemberRequest $request, Team $team)
    {
        $this->authorize('isCaptain', $team);

        $this->team_repository->removeMember($team, $request->user_id);

        return response()->json(['message' => 'Member removed successfully']);
    }

    public function leaveTeam(Request $request, Team $team)
    {
        if (! $this->team_repository->isMemberOf($team, Auth::id())) {
            throw new \Exception('you must be member of team');
        }

        $this->team_repository->removeMember($team, Auth::id());

        return response()->json(['message' => 'Member leaved successfully']);
    }

}
