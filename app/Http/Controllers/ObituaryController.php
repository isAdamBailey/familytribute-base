<?php

namespace App\Http\Controllers;

use App\Models\Obituary;
use App\Models\Person;
use App\Models\Story;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Storage;

class ObituaryController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'content' => 'required|string',
            'birth_date' => 'required|date',
            'death_date' => 'required|date',
            'photo' => 'file|mimes:jpg,jpeg,png|max:1024',
            'background_photo' => 'file|mimes:jpg,jpeg,png|max:1024|nullable',
        ]);

        $person = Person::create([
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
        ]);

        $mainPhotoUrl = $request->file('photo')
            ? $request->file('photo')->storePublicly('obituaries')
            : '';

        $backgroundPhotoUrl = $request->file('background_photo')
            ? $request->file('background_photo')->storePublicly('obituaries')
            : null;

        Obituary::create([
            'person_id' => $person->id,
            'content' => $request->input('content'),
            'birth_date' => Carbon::parse($request->birth_date)->toDateString(),
            'death_date' => Carbon::parse($request->death_date)->toDateString(),
            'main_photo_url' => $mainPhotoUrl,
            'background_photo_url' => $backgroundPhotoUrl,
        ]);

        return back()->with('flash.banner', 'Obituary successfully created!');
    }

    public function update(Request $request, $id): Redirector|Application|RedirectResponse
    {
        $request->validate([
            'first_name' => 'string|max:100',
            'last_name' => 'string|max:100',
            'content' => 'string',
            'birth_date' => 'date',
            'death_date' => 'date',
            'photo' => 'file|mimes:jpg,jpeg,png|max:1024',
            'background_photo' => 'file|mimes:jpg,jpeg,png|max:1024|nullable',
        ]);

        $obituary = Obituary::find($id);

        if ($request->input('first_name')) {
            $obituary->person->first_name = $request->input('first_name');
        }

        if ($request->input('last_name')) {
            $obituary->person->last_name = $request->input('last_name');
        }

        if ($request->input('content')) {
            $obituary->content = $request->input('content');
        }

        if ($request->input('birth_date')) {
            $obituary->birth_date = Carbon::parse($request->birth_date)->toDateString();
        }

        if ($request->input('death_date')) {
            $obituary->death_date = Carbon::parse($request->death_date)->toDateString();
        }

        if ($request->file('photo')) {
            if (! empty($obituary->main_photo_url)) {
                Storage::disk('s3')->delete($obituary->main_photo_url);
            }
            $obituary->main_photo_url = $request->file('photo')->storePublicly('obituaries');
        }

        if ($request->file('background_photo')) {
            if (! empty($obituary->background_photo_url)) {
                Storage::disk('s3')->delete($obituary->background_photo_url);
            }
            $obituary->background_photo_url = $request->file('background_photo')->storePublicly('obituaries');
        }

        $obituary->save();
        $obituary->person->save();

        return redirect(route('people.show', $obituary->person->fresh()))
            ->with('flash.banner', 'Obituary successfully updated!');
    }

    public function destroy($id): Redirector|Application|RedirectResponse
    {
        $obituary = Obituary::find($id);

        Storage::disk('s3')->delete($obituary->main_photo_url);
        Storage::disk('s3')->delete($obituary->background_photo_url);

        $obituary->person->stories()->detach();
        $obituary->person->pictures()->detach();
        $obituary->person->delete();
        $obituary->delete();

        return redirect(route('people.index'))
            ->with('flash.banner', 'Obituary successfully deleted!');
    }
}
