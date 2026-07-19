<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PersonResource;
use App\Models\Obituary;
use App\Models\Person;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PersonController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $sort = $request->sort;
        $order = $request->order;
        $search = $request->search;

        $people = Person::with('obituary')
            ->when($search,
                fn ($query) => $query->where('first_name', 'LIKE', '%'.$search.'%')
                    ->orWhere('last_name', 'LIKE', '%'.$search.'%')
            )
            ->when(! $sort || in_array($sort, ['death_date', 'birth_date']),
                fn ($query) => $query->orderBy(
                    Obituary::select($sort ?: 'death_date')
                        ->whereColumn('obituaries.person_id', 'people.id'),
                    $order ?: 'desc'
                ),
                fn ($query) => $query->orderBy($sort, $order ?: 'desc')
            )
            ->paginate();

        $people->appends($request->all());

        return response()->json([
            'people' => PersonResource::collection($people)->response($request)->getData(true),
            'sort' => ucwords(str_replace('_', ' ', $sort)),
            'order' => strtoupper($order),
            'search' => $search,
        ]);
    }

    public function show(Person $person): JsonResponse
    {
        $person = $person->load(['obituary', 'pictures', 'stories', 'parents', 'children']);

        return response()->json([
            'person' => PersonResource::make($person),
            'people' => Person::allForTagging(),
        ]);
    }
}
