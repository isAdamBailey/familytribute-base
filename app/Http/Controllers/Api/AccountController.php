<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Actions\ConfirmPassword;
use Laravel\Jetstream\Agent;
use Laravel\Jetstream\Contracts\DeletesUsers;

class AccountController extends Controller
{
    /**
     * JSON equivalent of Jetstream's UserProfileController@sessions.
     */
    public function sessions(Request $request): JsonResponse
    {
        if (config('session.driver') !== 'database') {
            return response()->json(['sessions' => []]);
        }

        $sessions = DB::connection(config('session.connection'))
            ->table(config('session.table', 'sessions'))
            ->where('user_id', $request->user()->getAuthIdentifier())
            ->orderBy('last_activity', 'desc')
            ->get()
            ->map(function ($session) use ($request) {
                $agent = tap(new Agent, fn ($agent) => $agent->setUserAgent($session->user_agent));

                return [
                    'agent' => [
                        'is_desktop' => $agent->isDesktop(),
                        'platform' => $agent->platform(),
                        'browser' => $agent->browser(),
                    ],
                    'ip_address' => $session->ip_address,
                    'is_current_device' => $session->id === $request->session()->getId(),
                    'last_active' => Carbon::createFromTimestamp($session->last_activity)->diffForHumans(),
                ];
            });

        return response()->json(['sessions' => $sessions]);
    }

    /**
     * JSON equivalent of Jetstream's OtherBrowserSessionsController@destroy.
     */
    public function destroyOtherSessions(Request $request, StatefulGuard $guard): JsonResponse
    {
        $this->confirmPasswordOrFail($request, $guard);

        $guard->logoutOtherDevices($request->password);

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
     * JSON equivalent of Jetstream's CurrentUserController@destroy.
     */
    public function destroy(Request $request, StatefulGuard $guard): JsonResponse
    {
        $this->confirmPasswordOrFail($request, $guard);

        app(DeletesUsers::class)->delete($request->user()->fresh());

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
