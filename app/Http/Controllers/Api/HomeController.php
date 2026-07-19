<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Picture;
use Illuminate\Http\JsonResponse;

class HomeController extends Controller
{
    public function show(): JsonResponse
    {
        return response()->json([
            'pictures' => Picture::where('featured', 1)->inRandomOrder()->limit(5)->get()->map(fn ($picture) => [
                'url' => $picture->url,
                'title' => $picture->title,
                'description' => $picture->description,
            ]),
        ]);
    }
}
