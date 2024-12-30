<?php

namespace Tests\Feature;

use App\Models\Team;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TournamentTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_participate_in_a_tournament_with_sufficient_balance()
    {
        $user = User::factory()->create(['balance' => 100.00]);
        $this->actingAs($user);


        $manager = User::factory()->create(['balance' => 100.00]);
        $tournament = Tournament::factory()->create(['entry_fee' => 50.00, 'manager_id' => $manager->id]);

        $response = $this->postJson(route('tournament.add-participant', $tournament->id));

        $response->assertStatus(200)
            ->assertJson(['message' => 'Successfully joined the tournament']);

        $this->assertDatabaseHas('tournament_participants', [
            'tournament_id' => $tournament->id,
            'user_id' => $user->id,
        ]);

        $this->assertEquals(50.00, $user->fresh()->balance);
    }

    public function test_user_cannot_participate_in_a_tournament_with_insufficient_balance()
    {
        $user = User::factory()->create(['balance' => 30.00]);
        $this->actingAs($user);

        $manager = User::factory()->create(['balance' => 100.00]);
        $tournament = Tournament::factory()->create(['entry_fee' => 50.00, 'manager_id' => $manager->id]);

        $response = $this->postJson(route('tournament.add-participant', $tournament->id));

        $response->assertStatus(400)
            ->assertSee('Insufficient balance');

        $this->assertDatabaseMissing('tournament_participants', [
            'tournament_id' => $tournament->id,
            'user_id' => $user->id,
        ]);

        $this->assertEquals(30.00, $user->fresh()->balance);
    }

    public function test_team_can_participate_in_a_tournament_by_captain()
    {
        $captain = User::factory()->create();
        $team = Team::factory()->create(['captain_id' => $captain->id]);
        $team->members()->attach(User::factory()->count(3)->create());

        $this->actingAs($captain);

        $tournament = Tournament::factory()->create(['manager_id' => $captain->id]);

        $response = $this->postJson(route('tournament.participate-team', [
            'tournament' => $tournament->id,
            'team' => $team->id,
        ]));

        $response->assertStatus(200)
            ->assertJson(['message' => 'Team Successfully joined the tournament']);

        foreach ($team->members as $member) {
            $this->assertDatabaseHas('tournament_participants', [
                'tournament_id' => $tournament->id,
                'user_id' => $member->id,
            ]);
        }
    }

    public function test_non_captain_user_cannot_register_team_in_tournament()
    {
        $captain = User::factory()->create();
        $nonCaptain = User::factory()->create();
        $team = Team::factory()->create(['captain_id' => $captain->id]);
        $team->members()->attach(User::factory()->count(3)->create());

        $this->actingAs($nonCaptain);

        $tournament = Tournament::factory()->create(['manager_id' => $captain->id]);

        $response = $this->postJson(route('tournament.participate-team', [
            'tournament' => $tournament->id,
            'team' => $team->id,
        ]));

        $response->assertStatus(403);

        foreach ($team->members as $member) {
            $this->assertDatabaseMissing('tournament_participants', [
                'tournament_id' => $tournament->id,
                'user_id' => $member->id,
            ]);
        }
    }

    // CRUD :

    public function test_can_create_tournament()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $data = [
            'name' => 'Test Tournament',
            'entry_fee' => 100.00,
        ];

        $response = $this->postJson(route('tournaments.store'), $data);

        $response->assertStatus(201)
            ->assertJsonFragment(['name' => 'Test Tournament']);

        $this->assertDatabaseHas('tournaments', [
            'name' => 'Test Tournament',
            'manager_id' => $user->id,
        ]);
    }

    public function test_can_list_tournaments()
    {
        Tournament::factory()->count(5)->create();

        $response = $this->getJson(route('tournaments.index'));

        $response->assertStatus(200)
            ->assertJsonCount(5, 'data');
    }

    public function test_can_show_tournament()
    {
        $tournament = Tournament::factory()->create();

        $response = $this->getJson(route('tournaments.show', $tournament->id));

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => $tournament->name]);
    }

    public function test_can_update_tournament()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $tournament = Tournament::factory()->create(['manager_id' => $user->id]);

        $updatedData = [
            'name' => 'Updated Tournament Name',
            'entry_fee' => 200.00,
        ];

        $response = $this->putJson(route('tournaments.update', $tournament->id), $updatedData);

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'Updated Tournament Name']);

        $this->assertDatabaseHas('tournaments', [
            'id' => $tournament->id,
            'name' => 'Updated Tournament Name',
        ]);
    }

    public function test_can_delete_tournament()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $tournament = Tournament::factory()->create(['manager_id' => $user->id]);

        $response = $this->deleteJson(route('tournaments.destroy', $tournament->id));

        $response->assertStatus(200)
            ->assertJson(['message' => 'Tournament deleted successfully']);

        $this->assertDatabaseMissing('tournaments', [
            'id' => $tournament->id,
        ]);
    }



}
