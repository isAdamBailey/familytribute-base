<?php

namespace App\Http\Middleware;

use App\Models\Team;
use Closure;
use Illuminate\Http\Request;

class EnsureHasTeam
{
    public function handle(Request $request, Closure $next)
    {
        if (! Team::first()) {
            return redirect()->route('teams.create');
        }
        $this->ensureUserHasCurrentTeamSet();

        return $next($request);
    }

    protected function ensureUserHasCurrentTeamSet(): void
    {
        $user = auth()->user();
        if (! $user->isMemberOfATeam()) {
            $team = Team::first();
            $team->users()->attach($user, ['role' => 'editor']);
            $user->switchTeam($team);

            if (is_null($user->current_team_id)) {
                $user->current_team_id = $team->id;
                $user->save();
            }
        }
    }
}
