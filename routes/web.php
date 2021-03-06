<?php

use App\Http\Controllers\Account\AccountController;
use App\Http\Controllers\Account\UserAddressController;
use App\Http\Controllers\AdministrationController;
use App\Http\Controllers\Auth\SocialiteCallbackController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MonicaController;
use App\Http\Controllers\OfficeLifeController;
use App\Http\Controllers\Profile\UserTokenController;
use Carbon\Carbon;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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
    Route::post('officelife/{plan}', [OfficeLifeController::class, 'create'])->name('officelife.create');
    Route::post('officelife/{plan}/price', [OfficeLifeController::class, 'price'])->name('officelife.price');
    Route::patch('officelife', [OfficeLifeController::class, 'update'])->name('officelife.update');
    Route::patch('officelife/paddleUpdate', [OfficeLifeController::class, 'paddleUpdate'])->name('officelife.update.paddle');
    Route::patch('officelife/paddleCancel', [OfficeLifeController::class, 'paddleCancel'])->name('officelife.cancel.paddle');

    // monica
    Route::get('monica', [MonicaController::class, 'index'])->name('monica.index');
    Route::post('monica/{plan}', [MonicaController::class, 'create'])->name('monica.create');
    Route::patch('monica', [MonicaController::class, 'update'])->name('monica.update');
    Route::patch('monica/paddleUpdate', [MonicaController::class, 'paddleUpdate'])->name('monica.update.paddle');
    Route::patch('monica/paddleCancel', [MonicaController::class, 'paddleCancel'])->name('monica.cancel.paddle');

    Route::delete('', [DashboardController::class, 'destroy'])->name('dashboard.destroy');

    Route::prefix('administration')->group(function () {
        Route::get('', [AdministrationController::class, 'index'])->name('administration.index');
        Route::get('{user}', [AdministrationController::class, 'show'])->name('administration.user.show');
    });

    // User & Profile...
    Route::get('user/account', [AccountController::class, 'show'])->name('account.show');
    Route::patch('user/address', [UserAddressController::class, 'update'])->name('user-address.update');
    Route::delete('auth/{driver}', [UserTokenController::class, 'destroy'])->name('provider.delete');
});
