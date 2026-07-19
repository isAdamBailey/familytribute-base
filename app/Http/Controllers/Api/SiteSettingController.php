<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SiteSettingController extends Controller
{
    public function show(): JsonResponse
    {
        return response()->json([
            'settings' => SiteSetting::first()?->only(['id', 'title', 'description', 'registration']),
        ]);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $request->validate([
            'title' => 'string|max:100',
            'description' => 'string',
            'registration_secret' => 'string|max:100',
            'registration' => 'boolean',
        ]);

        $settings = SiteSetting::find($id);

        if ($request->registration_secret) {
            $settings->registration_secret = $request->registration_secret;
        }

        if ($request->title) {
            $settings->title = $request->title;
        }

        if ($request->description) {
            $settings->description = $request->description;
        }

        if (isset($request->registration)) {
            $settings->registration = $request->registration;
        }

        $settings->save();

        return response()->json([
            'settings' => $settings->fresh()->only(['id', 'title', 'description', 'registration']),
        ]);
    }
}
