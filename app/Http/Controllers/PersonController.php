<?php

namespace App\Http\Controllers;

use App\Models\Obituary;
use App\Models\Person;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PersonController extends Controller
{
    public function index(Request $request): Response
    {
        $sort = $request->input('sort') ?: 'death_date';
        $order = $request->input('order') ?: 'desc';
        $search = $request->input('search');

        $people = Person::with('obituary')
            ->when($search,
                fn ($query) => $query->where('first_name', 'LIKE', '%'.$search.'%')
                        ->orWhere('last_name', 'LIKE', '%'.$search.'%')
            )
            ->when(in_array($sort, ['death_date', 'birth_date']),
                fn ($query) => $query->orderBy(
                    Obituary::select($sort)
                        ->whereColumn('obituaries.person_id', 'people.id'),
                    $order
                ),
                fn ($query) => $query->orderBy($sort, $order)
            )
            ->paginate();

        $people->appends([
            'sort' => $sort,
            'order' => $order,
            'search' => $search,
        ]);

        return Inertia::render('People', [
            'people' => $people->through(fn ($person) => [
                'slug' => $person->slug,
                'full_name' => $person->full_name,
                'obituary' => $person->obituary,
            ]),
            'sort' => $sort,
            'order' => $order,
            'search' => $search,
        ]);
    }

    public function show(Person $person): Response
    {
        $person = $person->load(['obituary', 'pictures', 'stories']);

        return Inertia::render('Person', [
            'person' => $person->only(
                'full_name',
                'first_name',
                'last_name',
                'obituary',
                'pictures',
                'stories'
            ),
        ]);
    }
}
