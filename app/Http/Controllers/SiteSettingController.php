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
            'registration_secret' => 'string|max:100',
            'registration' => 'boolean',
        ]);

        $settings = SiteSetting::find($id);

        if (isset($request->registration_secret)) {
            $settings->registration_secret = $request->registration_secret;
        }

        if (isset($request->registration)) {
            $settings->registration = $request->registration;
        }

        $settings->save();

        return redirect(route('dashboard'))
            ->with('flash.banner', 'Settings successfully updated!');
    }
}
