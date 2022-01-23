<?php

namespace App\Http\Controllers;

use App\Models\Person;
use App\Models\Story;
use App\Traits\HasSeoTags;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Inertia\Inertia;
use Inertia\Response;

class StoryController extends Controller
{
    use HasSeoTags;

    public function index(Request $request): Response
    {
        $this->setTitleStart('Stories');
        $this->renderSeo();

        $search = $request->search;
        $sort = $request->sort;
        $order = $request->order;

        $stories = Story::query()
            ->when(! auth()->user(),
                fn ($query) => $query->where('private', '!=', 1)
            )
            ->when($search,
                fn ($query) => $query->where('title', 'LIKE', '%'.$search.'%')
                    ->orWhere('excerpt', 'LIKE', '%'.$search.'%')
                    ->orWhere('content', 'LIKE', '%'.$search.'%')
            )
            ->orderBy($sort ?: 'year', $order ?: 'asc')
            ->orderBy('id', $order ?: 'asc')
            ->paginate();

        return Inertia::render('Stories', [
            'stories' => $stories->through(fn ($story) => [
                'slug' => $story->slug,
                'title' => $story->title,
                'excerpt' => $story->excerpt,
                'private' => $story->private,
                'year' => $story->year,
            ]),
            'sort' => ucwords(str_replace('_', ' ', $sort)),
            'order' => strtoupper($order),
            'search' => $search,
        ]);
    }

    public function show(Story $story): Response|RedirectResponse
    {
        if ($story->private && ! auth()->user()) {
            return redirect(route('stories.index'));
        }

        $this->setTitleStart($story->title);
        $this->setDescription($story->excerpt);
        $this->renderSeo();

        return Inertia::render('Story', [
            'story' => $story->only([
                'slug',
                'title',
                'content',
                'year',
                'excerpt',
                'private',
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
            'year' => 'digits:4|nullable',
            'private' => 'required|boolean',
            'person_ids' => 'array|nullable',
        ]);

        $story = Story::create([
            'title' => $request->title,
            'excerpt' => $request->excerpt,
            'private' => $request->private,
            'content' => $request->input('content'),
            'year' => $request->year,
        ]);

        if ($request->person_ids) {
            $story->people()->sync($request->person_ids);
        }

        return back()->with('flash.banner', 'Story successfully created!');
    }

    public function update(Request $request, $slug): Redirector|Application|RedirectResponse
    {
        $request->validate([
            'title' => 'string|max:100',
            'excerpt' => 'string|max:250',
            'content' => 'string',
            'year' => 'digits:4|nullable',
            'private' => 'boolean',
            'person_ids' => 'array|nullable',
        ]);

        $story = Story::where('slug', $slug)->first();

        if ($request->title) {
            $story->title = $request->title;
        }

        if ($request->excerpt) {
            $story->excerpt = $request->excerpt;
        }

        if (isset($request->private)) {
            $story->private = $request->private;
        }

        if ($request->input('content')) {
            $story->content = $request->input('content');
        }

        if ($request->year) {
            $story->year = $request->year;
        }

        if ($request->person_ids) {
            $story->people()->sync($request->person_ids);
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
