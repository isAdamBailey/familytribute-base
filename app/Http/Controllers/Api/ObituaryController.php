<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PersonResource;
use App\Models\Obituary;
use App\Models\Person;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ObituaryController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'content' => 'required|string',
            'birth_date' => 'required|date',
            'death_date' => 'required|date',
            'photo' => 'file|mimes:jpg,jpeg,png,svg,webp|max:20000|nullable',
            'background_photo' => 'file|mimes:jpg,jpeg,png,svg,webp|max:20000|nullable',
            'parent_ids' => 'array|nullable',
            'child_ids' => 'array|nullable',
        ]);

        $mainPhotoUrl = $request->file('photo')
            ? $request->file('photo')->storePublicly('obituaries', 's3')
            : '';

        $backgroundPhotoUrl = $request->file('background_photo')
            ? $request->file('background_photo')->storePublicly('obituaries', 's3')
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

        return response()->json([
            'person' => PersonResource::make($person->fresh()->load('obituary')),
        ], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $request->validate([
            'first_name' => 'string|max:100',
            'last_name' => 'string|max:100',
            'content' => 'string',
            'birth_date' => 'date',
            'death_date' => 'date',
            'photo' => 'file|mimes:jpg,jpeg,png,svg,webp|max:20000|nullable',
            'background_photo' => 'file|mimes:jpg,jpeg,png,svg,webp|max:20000|nullable',
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
            $person->photo_url = $request->file('photo')->storePublicly('obituaries', 's3');
        }

        if (isset($request->parent_ids)) {
            $person->parents()->sync($request->parent_ids);
        }

        if (isset($request->child_ids)) {
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
            $obituary->background_photo_url = $request->file('background_photo')->storePublicly('obituaries', 's3');
        }

        $obituary->save();
        $person->save();

        return response()->json([
            'person' => PersonResource::make($obituary->person->fresh()->load('obituary')),
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $obituary = Obituary::find($id);
        $person = $obituary->person;

        $photoPath = $person->getRawOriginal('photo_url');
        if (is_string($photoPath) && $photoPath !== '' && ! str_starts_with($photoPath, 'https://')) {
            Storage::disk('s3')->delete($photoPath);
        }

        $backgroundPath = $obituary->getRawOriginal('background_photo_url');
        if (is_string($backgroundPath) && $backgroundPath !== '' && ! str_starts_with($backgroundPath, 'https://')) {
            Storage::disk('s3')->delete($backgroundPath);
        }

        $person->stories()->detach();
        $person->pictures()->detach();
        $person->parents()->detach();
        $person->children()->detach();
        $person->delete();
        $obituary->delete();

        return response()->json(null, 204);
    }
}
