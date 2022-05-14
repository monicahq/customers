<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ValidationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('validate/{key}', [ValidationController::class, 'index'])
    ->middleware(['client:manage-key'])
    ->name('validation.index');
