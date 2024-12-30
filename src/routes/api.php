<?php

use App\Http\Controllers\TeamController;
use App\Http\Controllers\TournamentController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::apiResource('users', UserController::class);
Route::apiResource('teams', TeamController::class);
Route::apiResource('tournaments', TournamentController::class);

Route::post('teams/{team}/add-member', [TeamController::class, 'addMember'])->name('team.add-member');
Route::post('teams/{team}/remove-member', [TeamController::class, 'removeMember'])->name('team.remove-member');
Route::post('teams/{team}/leave-team', [TeamController::class, 'leaveTeam'])->name('team.leave-team');

Route::post('tournaments/{tournament}/add-participant', [TournamentController::class, 'participate'])->name('tournament.add-participant');
Route::post('tournaments/{tournament}/participate-team/{team}', [TournamentController::class, 'participateTeam'])->name('tournament.participate-team');
