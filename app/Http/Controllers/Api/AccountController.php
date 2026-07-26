<?php

namespace App\Http\Controllers\Api;

use App\Actions\DeleteUser;
use App\Http\Controllers\Controller;
use App\Support\UserAgent;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Actions\ConfirmPassword;

class AccountController extends Controller
{
    /**
     * List the user's active sessions, for the account settings "log out
     * other sessions" screen.
     */
    public function sessions(Request $request): JsonResponse
    {
        if (config('session.driver') !== 'database') {
            return response()->json(['sessions' => []]);
        }

        $currentSessionId = $request->session()->getId();

        $sessions = DB::connection(config('session.connection'))
            ->table(config('session.table', 'sessions'))
            ->where('user_id', $request->user()->getAuthIdentifier())
            ->orderBy('last_activity', 'desc')
            ->get()
            ->map(function ($session) use ($currentSessionId) {
                return [
                    'agent' => [
                        'is_desktop' => UserAgent::isDesktop($session->user_agent),
                        'platform' => UserAgent::platform($session->user_agent),
                        'browser' => UserAgent::browser($session->user_agent),
                    ],
                    'ip_address' => $session->ip_address,
                    'is_current_device' => $session->id === $currentSessionId,
                    'last_active' => Carbon::createFromTimestamp($session->last_activity)->diffForHumans(),
                ];
            });

        return response()->json(['sessions' => $sessions]);
    }

    /**
     * Log out every session for the user except the current one.
     *
     * This app never issues "remember me" cookies (the Nuxt SPA login never
     * sends `remember`), which makes `StatefulGuard::logoutOtherDevices()`
     * hit a null-handling bug in `Arr::last()`/`Arr::from()` on Laravel
     * 13.22+ (CookieJar::queued() passes a null $queued through). Deleting
     * the other session rows below already achieves the same end result for
     * a database session driver, so the buggy call is skipped rather than
     * worked around.
     *
     * Only actually invalidates anything under the `database` session driver
     * (config/session.php, this app's documented default — see .env.example)
     * — a non-database driver has no queryable-by-user_id session store to
     * delete rows from, so this silently reports success without logging
     * anything out. That constraint is pre-existing: `sessions()` above has
     * the identical gate, since listing sessions has the same requirement.
     */
    public function destroyOtherSessions(Request $request, StatefulGuard $guard): JsonResponse
    {
        $this->confirmPasswordOrFail($request, $guard);

        if (config('session.driver') === 'database') {
            DB::connection(config('session.connection'))
                ->table(config('session.table', 'sessions'))
                ->where('user_id', $request->user()->getAuthIdentifier())
                ->where('id', '!=', $request->session()->getId())
                ->delete();
        }

        return response()->json(['status' => 'ok']);
    }

    /**
     * Delete the current user's account.
     */
    public function destroy(Request $request, StatefulGuard $guard): JsonResponse
    {
        $this->confirmPasswordOrFail($request, $guard);

        app(DeleteUser::class)->delete($request->user()->fresh());

        $guard->logout();

        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return response()->json(['status' => 'ok']);
    }

    /**
     * @throws ValidationException if the given password doesn't match the current user's.
     */
    private function confirmPasswordOrFail(Request $request, StatefulGuard $guard): void
    {
        $confirmed = app(ConfirmPassword::class)($guard, $request->user(), $request->password);

        if (! $confirmed) {
            throw ValidationException::withMessages([
                'password' => __('The password is incorrect.'),
            ]);
        }
    }
}
