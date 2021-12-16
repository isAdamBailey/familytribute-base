<?php

namespace App\Http\Controllers;

use App\Models\Person;
use App\Models\Picture;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class PictureController extends Controller
{
    public function index(Request $request): Response
    {
        $sort = $request->input('sort') ?: 'year';
        $order = $request->input('order') ?: 'asc';
        $search = $request->input('search');

        $pictures = Picture::query()
            ->when($search,
                fn ($query) => $query->where('title', 'LIKE', '%'.$search.'%')
                    ->orWhere('description', 'LIKE', '%'.$search.'%')
                    ->orWhere('year', 'LIKE', '%'.$search.'%')
            )
            ->orderBy($sort, $order)
            ->paginate();

        $pictures->appends([
            'sort' => $sort,
            'order' => $order,
            'search' => $search,
        ]);

        return Inertia::render('Pictures', [
            'pictures' => $pictures->through(fn ($picture) => [
                'slug' => $picture->slug,
                'title' => $picture->title,
                'year' => $picture->year,
                'url' => $picture->url,
                'description' => $picture->description,
            ]),
            'sort' => $sort,
            'order' => $order,
            'search' => $search,
        ]);
    }

    public function show(Picture $picture): Response
    {
        return Inertia::render('Picture', [
            'picture' => $picture->only(
                'slug',
                'title',
                'url',
                'year',
                'featured',
                'description',
                'people',
                'person_ids'
            ),
            'people' => Person::all()->map(fn ($person) => [
                'id' => $person->id,
                'full_name' => $person->full_name,
            ]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string|max:100',
            'description' => 'required|string',
            'year' => 'required|digits:4',
            'featured' => 'required|integer',
            'photo' => 'required|file',
            'person_ids' => 'array|nullable',
        ]);

        $picture = Picture::create([
            'title' => $request->title,
            'description' => $request->description,
            'year' => $request->year,
            'featured' => $request->featured,
            'url' => $request->file('photo')->storePublicly('pictures'),
        ]);

        if (! empty($request->person_ids)) {
            $picture->people()->sync($request->person_ids);
        }

        return back()->with('flash.banner', 'Picture successfully created!');
    }

    public function update(Request $request, $slug): Redirector|Application|RedirectResponse
    {
        $request->validate([
            'title' => 'string|max:100',
            'description' => 'string',
            'year' => 'digits:4',
            'featured' => 'integer',
            'photo' => 'file',
            'person_ids' => 'array|nullable',
        ]);

        $picture = Picture::where('slug', $slug)->first();

        if ($request->title) {
            $picture->title = $request->title;
        }

        if ($request->description) {
            $picture->description = $request->description;
        }

        if ($request->year) {
            $picture->year = $request->year;
        }

        if (isset($request->featured)) {
            $picture->featured = $request->featured;
        }

        if ($request->file('photo')) {
            Storage::disk('s3')->delete($picture->url);
            $picture->url = $request->file('photo')->storePublicly('pictures');
        }

        if (! empty($request->person_ids)) {
            $picture->people()->sync($request->person_ids);
        }

        $picture->save();

        return redirect(route('pictures.show', $picture->fresh()))
            ->with('flash.banner', 'Picture successfully updated!');
    }

    public function destroy($slug): Redirector|Application|RedirectResponse
    {
        $picture = Picture::where('slug', $slug)->first();

        $picture->people()->detach();
        Storage::disk('s3')->delete($picture->url);

        $picture->delete();

        return redirect(route('pictures.index'))
            ->with('flash.banner', 'Picture successfully deleted!');
    }
}
