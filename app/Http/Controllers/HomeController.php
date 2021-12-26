<?php

namespace App\Http\Controllers;

use App\Models\Picture;
use App\Traits\HasSeoTags;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    use HasSeoTags;

    public function show(): Response
    {
        $this->setTitleStart('Home');
        $this->renderSeo();

        return Inertia::render('Home', [
            'pictures' => Picture::where('featured', 1)->inRandomOrder()->limit(5)->get()->map(fn ($picture) => [
                'url' => $picture->url,
                'title' => $picture->title,
                'description' => $picture->description,
            ]),
        ]);
    }
}
