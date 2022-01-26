<?php

namespace App\Http\Controllers;

use App\Models\Obituary;
use App\Models\Person;
use App\Traits\HasSeoTags;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PersonController extends Controller
{
    use HasSeoTags;

    public function index(Request $request): Response
    {
        $this->setTitleStart('People');
        $this->renderSeo();

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

        return Inertia::render('People', [
            'people' => $people->through(fn ($person) => [
                'slug' => $person->slug,
                'full_name' => $person->full_name,
                'photo_url' => $person->photo_url,
                'obituary' => $person->obituary,
            ]),
            'sort' => ucwords(str_replace('_', ' ', $sort)),
            'order' => strtoupper($order),
            'search' => $search,
        ]);
    }

    public function show(Person $person): Response
    {
        $person = $person->load(['obituary', 'pictures', 'stories']);

        $this->setTitleStart($person->full_name);
        $this->setOgImage($person->photo_url);
        $this->setTwitterImage($person->photo_url);
        $this->setDescription($person->obituary->content);
        $this->renderSeo();

        return Inertia::render('Person', [
            'person' => $person->only(
                'full_name',
                'first_name',
                'last_name',
                'photo_url',
                'obituary',
                'pictures',
                'stories',
                'parents',
                'children',
                'parent_ids',
            ),
            'people' => Person::allForTagging(),
        ]);
    }
}
