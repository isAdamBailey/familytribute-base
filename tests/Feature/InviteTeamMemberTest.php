<?php

namespace Tests\Feature;

use App\Mail\TeamInvitation;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class InviteTeamMemberTest extends TestCase
{
    use RefreshDatabase;

    public function test_team_members_can_be_invited_to_team()
    {
        Mail::fake();

        $this->actingAs($user = User::factory()->withPersonalTeam()->create());

        $response = $this->post('/teams/'.$user->currentTeam->id.'/members', [
            'email' => 'test@example.com',
            'role' => 'admin',
        ]);

        Mail::assertSent(function (TeamInvitation $mail) {
            $settings = SiteSetting::first();

            return $mail->secret === $settings->registration_secret;
        });

        $this->assertCount(1, $user->currentTeam->fresh()->teamInvitations);
    }

    public function test_team_member_invitations_can_be_cancelled()
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());

        $invitation = $user->currentTeam->teamInvitations()->create([
            'email' => 'test@example.com',
            'role' => 'admin',
        ]);

        $response = $this->delete('/team-invitations/'.$invitation->id);

        $this->assertCount(0, $user->currentTeam->fresh()->teamInvitations);
    }
}
