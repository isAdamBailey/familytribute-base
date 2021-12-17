<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SiteSettingController extends Controller
{
    public function update(Request $request, $id): RedirectResponse
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

        return redirect(route('dashboard'))
            ->with('flash.banner', 'Settings successfully updated!');
    }
}
