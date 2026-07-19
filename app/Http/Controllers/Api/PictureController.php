<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PictureResource;
use App\Models\Person;
use App\Models\Picture;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PictureController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $sort = $request->sort;
        $order = $request->order;
        $search = $request->search;

        $pictures = Picture::query()
            ->when(! auth()->user(),
                fn ($query) => $query->where('private', '!=', 1)
            )
            ->when($search,
                fn ($query) => $query->where('title', 'LIKE', '%'.$search.'%')
                    ->orWhere('description', 'LIKE', '%'.$search.'%')
                    ->orWhere('year', 'LIKE', '%'.$search.'%')
            )
            ->orderBy($sort ?: 'year', $order ?: 'asc')
            ->orderBy('id', $order ?: 'asc')
            ->paginate();

        $pictures->appends($request->all());

        return response()->json([
            'pictures' => PictureResource::collection($pictures)->response($request)->getData(true),
            'sort' => ucwords(str_replace('_', ' ', $sort)),
            'order' => strtoupper($order),
            'search' => $search,
        ]);
    }

    public function show(Picture $picture): JsonResponse
    {
        if ($picture->private && ! auth()->user()) {
            abort(404);
        }

        return response()->json([
            'picture' => PictureResource::make($picture->load('people')),
            'people' => Person::allForTagging(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:100',
            'description' => 'required|string',
            'year' => 'required|digits:4',
            'featured' => 'required|boolean',
            'private' => 'required|boolean',
            'photo' => 'required|file|mimes:jpg,jpeg,png|max:20000',
            'person_ids' => 'array|nullable',
        ]);

        $picture = Picture::create([
            'title' => $request->title,
            'description' => $request->description,
            'year' => $request->year,
            'featured' => $request->featured,
            'private' => $request->private,
            'url' => $request->file('photo')->storePublicly('pictures', 's3'),
        ]);

        if (! empty($request->person_ids)) {
            $picture->people()->sync($request->person_ids);
        }

        return response()->json([
            'picture' => PictureResource::make($picture->fresh()->load('people')),
        ], 201);
    }

    public function update(Request $request, $slug): JsonResponse
    {
        $request->validate([
            'title' => 'string|max:100',
            'description' => 'string',
            'year' => 'digits:4',
            'featured' => 'boolean',
            'private' => 'boolean',
            'photo' => 'file|mimes:jpg,jpeg,png|max:20000',
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

        if (isset($request->private)) {
            $picture->private = $request->private;
        }

        if ($request->file('photo')) {
            Storage::disk('s3')->delete($picture->url);
            $picture->url = $request->file('photo')->storePublicly('pictures', 's3');
        }

        if (isset($request->person_ids)) {
            $picture->people()->sync($request->person_ids);
        }

        $picture->save();

        return response()->json([
            'picture' => PictureResource::make($picture->fresh()->load('people')),
        ]);
    }

    public function destroy($slug): JsonResponse
    {
        $picture = Picture::where('slug', $slug)->first();

        $picture->people()->detach();
        Storage::disk('s3')->delete($picture->url);

        $picture->delete();

        return response()->json(null, 204);
    }
}
