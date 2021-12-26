<?php

namespace App\Http\Controllers;

use App\Models\Person;
use App\Models\SiteSetting;
use App\Traits\HasSeoTags;
use Inertia\Inertia;

class DashboardController extends Controller
{
    use HasSeoTags;

    public function show()
    {
        $this->setTitleStart('Dashboard');
        $this->renderSeo();

        return Inertia::render('Dashboard/Show', [
            'people' => Person::all(),
            'settings' => SiteSetting::first()->only('id', 'title', 'description', 'registration', 'registration_secret'),
        ]);
    }
}
