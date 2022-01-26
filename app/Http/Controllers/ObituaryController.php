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
            'photo' => 'file|mimes:jpg,jpeg,png,svg,webp|max:2000|nullable',
            'background_photo' => 'file|mimes:jpg,jpeg,png,svg,webp|max:2000|nullable',
            'parent_ids' => 'array|nullable',
            'child_ids' => 'array|nullable',
        ]);

        $mainPhotoUrl = $request->file('photo')
            ? $request->file('photo')->storePublicly('obituaries')
            : '';

        $backgroundPhotoUrl = $request->file('background_photo')
            ? $request->file('background_photo')->storePublicly('obituaries')
            : null;

        $person = Person::create([
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'photo_url' => $mainPhotoUrl,
        ]);

        if ($request->parent_ids) {
            $person->parents()->sync($request->parent_ids);
        }

        if ($request->child_ids) {
            $person->children()->sync($request->child_ids);
        }

        Obituary::create([
            'person_id' => $person->id,
            'content' => $request->input('content'),
            'birth_date' => Carbon::parse($request->birth_date)->toDateString(),
            'death_date' => Carbon::parse($request->death_date)->toDateString(),
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
            'photo' => 'file|mimes:jpg,jpeg,png,svg,webp|max:2000|nullable',
            'background_photo' => 'file|mimes:jpg,jpeg,png,svg,webp|max:2000|nullable',
            'parent_ids' => 'array|nullable',
            'child_ids' => 'array|nullable',
        ]);

        $obituary = Obituary::find($id);
        $person = $obituary->person;

        if ($request->input('first_name')) {
            $person->first_name = $request->input('first_name');
        }

        if ($request->input('last_name')) {
            $person->last_name = $request->input('last_name');
        }

        if ($request->file('photo')) {
            if (! empty($person->photo_url)) {
                Storage::disk('s3')->delete($person->photo_url);
            }
            $person->photo_url = $request->file('photo')->storePublicly('obituaries');
        }

        if ($request->parent_ids) {
            $person->parents()->sync($request->parent_ids);
        }

        if ($request->child_ids) {
            $person->children()->sync($request->child_ids);
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

        if ($request->file('background_photo')) {
            if (! empty($obituary->background_photo_url)) {
                Storage::disk('s3')->delete($obituary->background_photo_url);
            }
            $obituary->background_photo_url = $request->file('background_photo')->storePublicly('obituaries');
        }

        $obituary->save();
        $person->save();

        return redirect(route('people.show', $obituary->person->fresh()))
            ->with('flash.banner', 'Obituary successfully updated!');
    }

    public function destroy($id): Redirector|Application|RedirectResponse
    {
        $obituary = Obituary::find($id);
        $person = $obituary->person;

        Storage::disk('s3')->delete($person->photo_url);
        Storage::disk('s3')->delete($obituary->background_photo_url);

        $person->stories()->detach();
        $person->pictures()->detach();
        $person->parents()->detach();
        $person->children()->detach();
        $person->delete();
        $obituary->delete();

        return redirect(route('people.index'))
            ->with('flash.banner', 'Obituary successfully deleted!');
    }
}
