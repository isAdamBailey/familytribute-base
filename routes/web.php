<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ObituaryController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\PictureController;
use App\Http\Controllers\SiteSettingController;
use App\Http\Controllers\StoryController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

//Route::get('/mailable', function () {
//    $data = [
//        'siteName' => App\Models\SiteSetting::first()->title,
//        'deathDates' => App\Models\Obituary::with('person')->inRandomOrder()->limit(3)->get(),
//        'birthDates' => App\Models\Obituary::with('person')->inRandomOrder()->limit(3)->get(),
//    ];
//
//    return new App\Mail\UpcomingDates($data);
//});

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

Route::fallback(function () {
    return Inertia::render('Error', ['status' => 404]);
})->name('404.show');
