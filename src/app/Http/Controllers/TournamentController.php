<?php

namespace App\Http\Controllers;

use App\Http\Requests\TournamentRequest;
use App\Http\Resources\TournamentResource;
use App\Repositories\TournamentRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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
        $tournament = $this->tournament_repository->create($request->validated());
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
}
