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
            'photo' => 'file',
        ]);

        $person = Person::create([
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
        ]);

        $headstoneUrl = $request->file('photo')
            ? $request->file('photo')->storePublicly('obituaries')
            : '';

        Obituary::create([
            'person_id' => $person->id,
            'content' => $request->input('content'),
            'birth_date' => Carbon::parse($request->birth_date)->toDateString(),
            'death_date' => Carbon::parse($request->death_date)->toDateString(),
            'headstone_url' => $headstoneUrl,
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
            'photo' => 'file',
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
            if (! empty($obituary->headstone_url)) {
                Storage::disk('s3')->delete($obituary->headstone_url);
            }
            $obituary->headstone_url = $request->file('photo')->storePublicly('obituaries');
        }

        $obituary->save();
        $obituary->person->save();

        return redirect(route('people.show', $obituary->person->fresh()))
            ->with('flash.banner', 'Obituary successfully updated!');
    }

    public function destroy($id): Redirector|Application|RedirectResponse
    {
        $obituary = Obituary::find($id);

        $obituary->person->stories()->detach();
        $obituary->person->pictures()->detach();
        $obituary->person->delete();
        $obituary->delete();

        return redirect(route('people.index'))
            ->with('flash.banner', 'Obituary successfully deleted!');
    }
}
