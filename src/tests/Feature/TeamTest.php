<?php

namespace Tests\Feature;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TeamTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_add_member_if_is_captain()
    {
        $captain = User::factory()->create();
        $this->actingAs($captain);

        $member = User::factory()->create();

        $team = Team::factory()->create(['captain_id'=>$captain->id]);

        $this->postJson(route('team.add-member', ['team'=>$team->id]), [
            'user_id' => $member->id
        ]);

        $this->assertDatabaseHas('team_members', [
            'team_id' => $team->id,
            'user_id' => $member->id,
            'is_captain' => false
        ]);
    }

    public function test_can_remove_member_if_is_captain()
    {
        $captain = User::factory()->create();
        $this->actingAs($captain);

        $member = User::factory()->create();

        $team = Team::factory()->create(['captain_id'=>$captain->id]);

        $team->members()->attach($member);

        $this->postJson(route('team.remove-member', ['team'=>$team->id]), [
            'user_id' => $member->id
        ]);

        $this->assertDatabaseMissing('team_members', [
            'team_id' => $team->id,
            'user_id' => $member->id,
            'is_captain' => false
        ]);
    }

    public function test_can_not_remove_member_if_is_not_captain()
    {
        $captain = User::factory()->create();

        $member = User::factory()->create();

        $anOtherMember = User::factory()->create();

        $this->actingAs($anOtherMember);

        $team = Team::factory()->create(['captain_id'=>$captain->id]);

        $team->members()->attach($member);

        $response = $this->postJson(route('team.remove-member', ['team'=>$team->id]), [
            'user_id' => $member->id
        ]);

        $response->assertStatus(403);
    }

    public function test_user_can_leave_team()
    {

        $member = User::factory()->create();

//        $anOtherMember = User::factory()->create();

        $this->actingAs($member);

        $team = Team::factory()->create();

        $team->members()->attach($member);

        $response = $this->postJson(route('team.leave-team', ['team'=>$team->id]));

        $response->assertStatus(200);

        $this->assertDatabaseMissing('team_members', [
            'team_id' => $team->id,
            'user_id' => $member->id,
            'is_captain' => false
        ]);

    }

    // CRUD
    public function test_can_create_team()
    {
        $captain = User::factory()->create();
        $this->actingAs($captain);

        $data = [
            'name' => 'Test Team',
        ];

        $response = $this->postJson(route('teams.store'), $data);

        $response->assertStatus(201)
            ->assertJsonFragment(['name' => 'Test Team']);

        $this->assertDatabaseHas('teams', [
            'name' => 'Test Team',
            'captain_id' => $captain->id,
        ]);

        $this->assertDatabaseHas('team_members', [
            'team_id' => $response['data']['id'],
            'user_id' => $captain->id,
            'is_captain' => true
        ]);
    }

    public function test_can_list_teams()
    {
        Team::factory()->count(5)->create();

        $response = $this->getJson(route('teams.index'));

        $response->assertStatus(200)
            ->assertJsonCount(5, 'data');
    }

    public function test_can_show_team()
    {
        $team = Team::factory()->create();

        $response = $this->getJson(route('teams.show', $team->id));

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => $team->name]);
    }

    public function test_can_update_team()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $team = Team::factory()->create(['captain_id' => $user->id]);

        $updatedData = [
            'name' => 'Updated Team',
        ];

        $response = $this->putJson(route('teams.update', $team->id), $updatedData);

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'Updated Team']);

        $this->assertDatabaseHas('teams', [
            'id' => $team->id,
            'name' => 'Updated Team',
        ]);
    }

    public function test_can_delete_team()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $team = Team::factory()->create(['captain_id' => $user->id]);

        $response = $this->deleteJson(route('teams.destroy', $team->id));

        $response->assertStatus(200)
            ->assertJson(['message' => 'Team deleted successfully']);

        $this->assertDatabaseMissing('teams', [
            'id' => $team->id,
        ]);
    }


}
