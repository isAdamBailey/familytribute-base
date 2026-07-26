<?php

namespace App\Actions\Fortify;

use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Create a newly registered user.
     *
     * @throws ValidationException
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
        ])->after(function ($validator) use ($input) {
            $settings = SiteSetting::first();
            if (! $settings->registration) {
                $validator->errors()->add('registration', __('Registration is not enabled.'));
            }
            if (! isset($input['registration_secret']) || ! Str::is($input['registration_secret'], $settings->registration_secret)) {
                $validator->errors()->add('registration_secret', __('The provided registration secret is not valid.'));
            }
        })->validate();

        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
        ]);
    }
}
