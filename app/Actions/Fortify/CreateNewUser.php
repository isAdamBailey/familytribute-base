<?php

namespace App\Actions\Fortify;

use App\Models\SiteSetting;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Create a newly registered user.
     *
     * @param  array  $input
     * @return User
     * @throws ValidationException
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['required', 'accepted'] : '',
        ])->after(function ($validator) use ($input) {
            $settings = SiteSetting::first();
            if (! $settings->registration) {
                $validator->errors()->add('registration', __('Registration is not enabled.'));
            }
            if (! isset($input['registration_secret']) || ! Str::is($input['registration_secret'], $settings->registration_secret)) {
                $validator->errors()->add('registration_secret', __('The provided registration secret is not valid.'));
            }
        })->validate();

        return DB::transaction(function () use ($input) {
            return tap(User::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => Hash::make($input['password']),
            ]), function (User $user) {
                $this->createTeam($user);
            });
        });
    }

    /**
     * Create a personal team for the user.
     *
     * @param  User  $user
     * @return void
     */
    protected function createTeam(User $user)
    {
        $team = Team::first();

        // if they are the first user to register to the site, then they are the administrator of the only team.
        if ($team) {
            // first one to log in is administrator, the rest are editors
            $team->users()->attach($user, ['role' => 'editor']);

            $user->switchTeam($team);
        } else {
            $user->switchTeam($team = $user->ownedTeams()->create([
                'name' => config('app.name'),
                'personal_team' => false,
            ]));

            $team->users()->attach($user, ['role' => 'admin']);
        }
    }
}
