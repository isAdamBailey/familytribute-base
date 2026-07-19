<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ObituaryController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\PictureController;
use App\Http\Controllers\SiteSettingController;
use App\Http\Controllers\StoryController;
use App\Http\Controllers\TeamRouteRemovedController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Route::get('/mailable', function () {
//    return new App\Mail\UpcomingDates(
//        App\Models\SiteSetting::first()->title,
//        App\Models\Obituary::with('person')->inRandomOrder()->limit(3)->get(),
//        App\Models\Obituary::with('person')->inRandomOrder()->limit(3)->get(),
//    );
// });
Route::get('/', [HomeController::class, 'show'])->name('home');
Route::get('/pictures', [PictureController::class, 'index'])->name('pictures.index');
Route::get('/pictures/{picture}', [PictureController::class, 'show'])->name('pictures.show');
Route::get('/stories', [StoryController::class, 'index'])->name('stories.index');
Route::get('/stories/{story}', [StoryController::class, 'show'])->name('stories.show');
Route::get('/people', [PersonController::class, 'index'])->name('people.index');
Route::middleware(['auth:sanctum', 'verified', 'team'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'show'])->name('dashboard');
    Route::post('/pictures', [PictureController::class, 'store'])->name('pictures.store');
    Route::put('/pictures/{picture}', [PictureController::class, 'update'])->name('pictures.update');
    Route::delete('/pictures/{picture}', [PictureController::class, 'destroy'])->name('pictures.destroy');
    Route::post('/obituaries', [ObituaryController::class, 'store'])->name('obituaries.store');
    Route::put('/obituaries/{obituary}', [ObituaryController::class, 'update'])->name('obituaries.update');
    Route::delete('/obituaries/{obituary}', [ObituaryController::class, 'destroy'])->name('obituaries.destroy');
    Route::post('/stories', [StoryController::class, 'store'])->name('stories.store');
    Route::put('/stories/{story}', [StoryController::class, 'update'])->name('stories.update');
    Route::delete('/stories/{story}', [StoryController::class, 'destroy'])->name('stories.destroy');
    Route::put('/site-settings/{id}', [SiteSettingController::class, 'update'])->name('site-settings.update');
});

Route::get('/{person}', [PersonController::class, 'show'])->name('people.show');

// Team-management is trimmed from this single-team app (Phase 1 of the
// Nuxt migration, issue #19). These shadow the vendor Jetstream team
// routes registered in JetstreamServiceProvider (loaded before this file),
// so requests 404 instead of reaching the vendor controllers. The Team
// model, membership plumbing, and EnsureHasTeam middleware stay intact.
Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('/teams/create', TeamRouteRemovedController::class);
    Route::post('/teams', TeamRouteRemovedController::class);
    Route::get('/teams/{team}', TeamRouteRemovedController::class);
    Route::put('/teams/{team}', TeamRouteRemovedController::class);
    Route::delete('/teams/{team}', TeamRouteRemovedController::class);
    Route::post('/teams/{team}/members', TeamRouteRemovedController::class);
    Route::put('/teams/{team}/members/{user}', TeamRouteRemovedController::class);
    Route::delete('/teams/{team}/members/{user}', TeamRouteRemovedController::class);
    Route::delete('/team-invitations/{invitation}', TeamRouteRemovedController::class);
});

Route::fallback(function () {
    return Inertia::render('Error', ['status' => 404]);
})->name('404.show');
