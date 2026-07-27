<?php

namespace App\Console\Commands;

use App\Models\Obituary;
use App\Models\Person;
use Illuminate\Console\Command;

class E2eEnsurePeopleCommand extends Command
{
    public const MARKER = 'E2EPaginate';

    protected $signature = 'e2e:ensure-people
                            {count=20 : Minimum number of marked people that should exist}
                            {--cleanup : Delete people created for pagination tests}';

    protected $description = 'Ensure marked people exist for Playwright pagination tests (local/e2e only)';

    public function handle(): int
    {
        if (! $this->allowed()) {
            $this->error('This command is only available when E2E_HELPERS=true or APP_ENV is local/testing.');

            return self::FAILURE;
        }

        if ($this->option('cleanup')) {
            $people = Person::query()
                ->where('last_name', 'like', self::MARKER.'%')
                ->get();

            foreach ($people as $person) {
                Obituary::where('person_id', $person->id)->delete();
                $person->stories()->detach();
                $person->pictures()->detach();
                $person->parents()->detach();
                $person->children()->detach();
                $person->delete();
            }

            $this->line((string) $people->count());

            return self::SUCCESS;
        }

        $target = max(1, (int) $this->argument('count'));
        $current = Person::query()
            ->where('last_name', 'like', self::MARKER.'%')
            ->count();
        $needed = $target - $current;

        for ($i = 0; $i < $needed; $i++) {
            Obituary::factory()->create([
                'person_id' => Person::factory()->create([
                    'first_name' => 'Page',
                    'last_name' => self::MARKER.($current + $i),
                ])->id,
            ]);
        }

        $this->line((string) Person::query()
            ->where('last_name', 'like', self::MARKER.'%')
            ->count());

        return self::SUCCESS;
    }

    private function allowed(): bool
    {
        return filter_var(env('E2E_HELPERS', false), FILTER_VALIDATE_BOOL)
            || in_array(app()->environment(), ['local', 'testing', 'e2e'], true);
    }
}
