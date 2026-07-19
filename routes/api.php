<?php

use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\ObituaryController;
use App\Http\Controllers\Api\PersonController;
use App\Http\Controllers\Api\PictureController;
use App\Http\Controllers\Api\SiteSettingController;
use App\Http\Controllers\Api\StoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
| This JSON API is additive: it serves the same data as the Inertia web
| routes (routes/web.php) via the same Resource classes, for consumption
| by the future Nuxt frontend. It does not replace or alter web.php.
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/home', [HomeController::class, 'show'])->name('api.home');
Route::get('/people', [PersonController::class, 'index'])->name('api.people.index');
Route::get('/people/{person}', [PersonController::class, 'show'])->name('api.people.show');
Route::get('/pictures', [PictureController::class, 'index'])->name('api.pictures.index');
Route::get('/pictures/{picture}', [PictureController::class, 'show'])->name('api.pictures.show');
Route::get('/stories', [StoryController::class, 'index'])->name('api.stories.index');
Route::get('/stories/{story}', [StoryController::class, 'show'])->name('api.stories.show');
Route::get('/site-settings', [SiteSettingController::class, 'show'])->name('api.site-settings.show');

Route::middleware(['auth:sanctum', 'verified', 'team'])->group(function () {
    Route::post('/pictures', [PictureController::class, 'store'])->name('api.pictures.store');
    Route::put('/pictures/{picture}', [PictureController::class, 'update'])->name('api.pictures.update');
    Route::delete('/pictures/{picture}', [PictureController::class, 'destroy'])->name('api.pictures.destroy');
    Route::post('/obituaries', [ObituaryController::class, 'store'])->name('api.obituaries.store');
    Route::put('/obituaries/{obituary}', [ObituaryController::class, 'update'])->name('api.obituaries.update');
    Route::delete('/obituaries/{obituary}', [ObituaryController::class, 'destroy'])->name('api.obituaries.destroy');
    Route::post('/stories', [StoryController::class, 'store'])->name('api.stories.store');
    Route::put('/stories/{story}', [StoryController::class, 'update'])->name('api.stories.update');
    Route::delete('/stories/{story}', [StoryController::class, 'destroy'])->name('api.stories.destroy');
    Route::put('/site-settings/{id}', [SiteSettingController::class, 'update'])->name('api.site-settings.update');
});
