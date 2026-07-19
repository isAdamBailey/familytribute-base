<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\URL;

class E2eSignedUrlCommand extends Command
{
    protected $signature = 'e2e:signed-url
                            {type : verification|password-reset}
                            {email : User email}';

    protected $description = 'Print a signed verification or password-reset URL for Playwright (local/e2e only)';

    public function handle(): int
    {
        if (! $this->allowed()) {
            $this->error('This command is only available when E2E_HELPERS=true or APP_ENV is local/testing.');

            return self::FAILURE;
        }

        $user = User::where('email', $this->argument('email'))->first();

        if (! $user) {
            $this->error('User not found.');

            return self::FAILURE;
        }

        $type = $this->argument('type');

        if ($type === 'verification') {
            $this->line(URL::temporarySignedRoute(
                'verification.verify',
                now()->addMinutes(60),
                ['id' => $user->id, 'hash' => sha1($user->email)]
            ));

            return self::SUCCESS;
        }

        if ($type === 'password-reset') {
            $token = Password::broker()->createToken($user);
            $this->line(
                url('/reset-password/'.$token).'?email='.urlencode($user->email)
            );

            return self::SUCCESS;
        }

        $this->error('Unknown type. Use verification or password-reset.');

        return self::FAILURE;
    }

    private function allowed(): bool
    {
        return filter_var(env('E2E_HELPERS', false), FILTER_VALIDATE_BOOL)
            || in_array(app()->environment(), ['local', 'testing', 'e2e'], true);
    }
}
