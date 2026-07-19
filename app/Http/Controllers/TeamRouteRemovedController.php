<?php

namespace App\Http\Controllers;

class TeamRouteRemovedController extends Controller
{
    public function __invoke(): never
    {
        abort(404);
    }
}
