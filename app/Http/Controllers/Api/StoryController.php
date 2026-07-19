<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\StoryResource;
use App\Models\Person;
use App\Models\Story;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StoryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
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

        $stories->appends($request->all());

        return response()->json([
            'stories' => StoryResource::collection($stories)->response($request)->getData(true),
            'sort' => ucwords(str_replace('_', ' ', $sort)),
            'order' => strtoupper($order),
            'search' => $search,
        ]);
    }

    public function show(Story $story): JsonResponse
    {
        if ($story->private && ! auth()->user()) {
            abort(404);
        }

        return response()->json([
            'story' => StoryResource::make($story->load('people')),
            'people' => Person::allForTagging(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:100',
            'excerpt' => 'required|string|max:250',
            'content' => 'required|string',
            'year' => 'digits:4|nullable',
            'private' => 'required|boolean',
            'person_ids' => 'array|nullable',
            'media' => 'nullable|file|mimes:mp3,mp4,wav,m4a,aac,ogg,webm,mov|max:102400',
        ]);

        $data = [
            'title' => $request->title,
            'excerpt' => $request->excerpt,
            'private' => $request->private,
            'content' => $request->input('content'),
            'year' => $request->year,
        ];

        if ($request->hasFile('media')) {
            $data['media_path'] = $request->file('media')->storePublicly('story-media', 's3');
        }

        $story = Story::create($data);

        if ($request->person_ids) {
            $story->people()->sync($request->person_ids);
        }

        return response()->json([
            'story' => StoryResource::make($story->fresh()->load('people')),
        ], 201);
    }

    public function update(Request $request, $slug): JsonResponse
    {
        $request->validate([
            'title' => 'string|max:100',
            'excerpt' => 'string|max:250',
            'content' => 'string',
            'year' => 'digits:4|nullable',
            'private' => 'boolean',
            'person_ids' => 'array|nullable',
            'media' => 'nullable|file|mimes:mp3,mp4,wav,m4a,aac,ogg,webm,mov|max:102400',
            'remove_media' => 'nullable|boolean',
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

        if (isset($request->person_ids)) {
            $story->people()->sync($request->person_ids);
        }

        if ($request->hasFile('media')) {
            if ($story->media_path) {
                Storage::disk('s3')->delete($story->media_path);
            }
            $story->media_path = $request->file('media')->storePublicly('story-media', 's3');
        } elseif ($request->boolean('remove_media') && $story->media_path) {
            Storage::disk('s3')->delete($story->media_path);
            $story->media_path = null;
        }

        $story->save();

        return response()->json([
            'story' => StoryResource::make($story->fresh()->load('people')),
        ]);
    }

    public function destroy($slug): JsonResponse
    {
        $story = Story::where('slug', $slug)->first();

        if ($story->media_path) {
            Storage::disk('s3')->delete($story->media_path);
        }

        $story->people()->detach();
        $story->delete();

        return response()->json(null, 204);
    }
}
