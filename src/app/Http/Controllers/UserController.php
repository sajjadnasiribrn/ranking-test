<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(private readonly UserRepository $user_repository){}

    public function index()
    {
        return UserResource::collection($this->user_repository->all());
    }

    public function show(User $user)
    {
        return new UserResource($user);
    }

    public function store(UserRequest $request)
    {
        $user = $this->user_repository->create($request->validated());

        return response()->json($user, 201);
    }

    public function update(UserRequest $request, $id)
    {
        $user = $this->user_repository->update($id, $request->validated());

        return new UserResource($user);
    }

    public function destroy($id)
    {
        $this->user_repository->delete($id);

        return response()->json(['message' => 'User deleted successfully']);
    }
}
