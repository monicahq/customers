<?php

use App\Http\Controllers\Auth\SocialiteCallbackController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MonicaController;
use App\Http\Controllers\OfficeLifeController;
use App\Http\Controllers\Profile\UserTokenController;
use Carbon\Carbon;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'year' => Carbon::now()->year,
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('home');

Route::middleware(['throttle:oauth2-socialite'])->group(function () {
    Route::get('auth/{driver}', [SocialiteCallbackController::class, 'login'])->name('login.provider');
    Route::get('auth/{driver}/callback', [SocialiteCallbackController::class, 'callback']);
    Route::post('auth/{driver}/callback', [SocialiteCallbackController::class, 'callback']);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // officelife
    Route::get('officelife', [OfficeLifeController::class, 'index'])->name('officelife.index');
    Route::post('officelife/{plan}/price', [OfficeLifeController::class, 'price'])->name('officelife.price');

    // monica
    Route::get('monica', [MonicaController::class, 'index'])->name('monica.index');

    // User & Profile...
    Route::delete('auth/{driver}', [UserTokenController::class, 'destroy'])->name('provider.delete');
});
