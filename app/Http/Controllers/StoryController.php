<?php

namespace App\Http\Controllers;

use App\Models\Person;
use App\Models\Story;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Inertia\Response;

class StoryController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->input('search');

        $stories = Story::query()
            ->when($search,
                fn ($query) => $query->where('title', 'LIKE', '%'.$search.'%')
                    ->orWhere('excerpt', 'LIKE', '%'.$search.'%')
                    ->orWhere('content', 'LIKE', '%'.$search.'%')
            )
            ->latest()
            ->paginate();

        $stories->appends([
            'search' => $search,
        ]);

        return Inertia::render('Stories', [
            'stories' => $stories->through(fn ($story) => [
                'slug' => $story->slug,
                'title' => $story->title,
                'excerpt' => $story->excerpt,
            ]),
            'search' => $search,
        ]);
    }

    public function show(Story $story): Response
    {
        return Inertia::render('Story', [
            'story' => $story->only([
                'slug',
                'title',
                'content',
                'excerpt',
                'people',
                'person_ids',
            ]),
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
            'excerpt' => 'required|string|max:250',
            'content' => 'required|string',
            'person_ids' => 'array|nullable',
        ]);

        $story = Story::create([
            'title' => $request->input('title'),
            'excerpt' => $request->input('excerpt'),
            'content' => $request->input('content'),
        ]);

        if ($request->input('person_ids')) {
            $story->people()->sync($request->input('person_ids'));
        }

        return back()->with('flash.banner', 'Story successfully created!');
    }

    public function update(Request $request, $slug): Redirector|Application|RedirectResponse
    {
        $request->validate([
            'title' => 'string|max:100',
            'excerpt' => 'string|max:250',
            'content' => 'string',
            'person_ids' => 'array|nullable',
        ]);

        $story = Story::where('slug', $slug)->first();

        if ($request->input('title')) {
            $story->title = $request->input('title');
        }

        if ($request->input('excerpt')) {
            $story->excerpt = $request->input('excerpt');
        }

        if ($request->input('content')) {
            $story->content = $request->input('content');
        }

        if ($request->input('person_ids')) {
            $story->people()->sync($request->input('person_ids'));
        }

        $story->save();

        return redirect(route('stories.show', $story->fresh()))
            ->with('flash.banner', 'Story successfully updated!');
    }

    public function destroy($slug): Redirector|Application|RedirectResponse
    {
        $story = Story::where('slug', $slug)->first();

        $story->people()->detach();
        $story->delete();

        return redirect(route('stories.index'))
            ->with('flash.banner', 'Story successfully deleted!');
    }
}
