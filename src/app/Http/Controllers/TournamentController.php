<?php

namespace App\Http\Controllers;

use App\Http\Requests\TournamentRequest;
use App\Http\Resources\TournamentResource;
use App\Repositories\TeamRepository;
use App\Repositories\TournamentRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TournamentController extends Controller
{
    public function __construct(private readonly TournamentRepository $tournament_repository){}

    public function index()
    {
        $tournaments = $this->tournament_repository->all();
        return TournamentResource::collection($tournaments);
    }

    public function store(TournamentRequest $request)
    {
        $data = $request->validated();
        $data['manager_id'] = Auth::id();

        $tournament = $this->tournament_repository->create($data);

        return new TournamentResource($tournament);
    }

    public function show($id)
    {
        $tournament = $this->tournament_repository->find($id);

        if (!$tournament) {
            return response()->json(['message' => 'Tournament not found'], Response::HTTP_NOT_FOUND);
        }

        return new TournamentResource($tournament);
    }

    public function update(TournamentRequest $request, $id)
    {
        $tournament = $this->tournament_repository->update($id, $request->validated());

        if (!$tournament) {
            return response()->json(['message' => 'Tournament not found'], Response::HTTP_NOT_FOUND);
        }

        return new TournamentResource($tournament);
    }

    public function destroy($id)
    {
        $deleted = $this->tournament_repository->delete($id);

        if (!$deleted) {
            return response()->json(['message' => 'Tournament not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json(['message' => 'Tournament deleted successfully']);
    }

    public function participate(Request $request, $id, UserRepository $user_repository)
    {
        $tournament = $this->tournament_repository->find($id);

        if (!$tournament) {
            return response()->json(['message' => 'Tournament not found'], Response::HTTP_NOT_FOUND);
        }

        DB::transaction(function () use ($tournament, $user_repository) {
            $user = auth()->user();

            if ($user->balance < $tournament->entry_fee) {
                abort(Response::HTTP_BAD_REQUEST, 'Insufficient balance');
            }

            $user_repository->update($user->id, [
                'balance' => $user->balance - $tournament->entry_fee
            ]);

            $this->tournament_repository->addParticipant($tournament, $user->id);
        });

        return response()->json(['message' => 'Successfully joined the tournament']);
    }

    /**
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function participateTeam(Request $request, $tournament, $team, TeamRepository $team_repository)
    {
        $tournament = $this->tournament_repository->find($tournament);
        $team = $team_repository->find($team);

        if (!$tournament) {
            return response()->json(['message' => 'Tournament not found'], Response::HTTP_NOT_FOUND);
        }
        if (!$team) {
            return response()->json(['message' => 'team not found'], Response::HTTP_NOT_FOUND);
        }

        $this->authorize('isCaptain', $team);
        $this->authorize('isManager', $tournament);

        $this->tournament_repository->participateTeam($tournament, $team);

        return response()->json(['message' => 'Team Successfully joined the tournament']);
    }
}
