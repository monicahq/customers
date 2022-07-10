<?php

use App\Http\Controllers\ValidateKeyController;
use Illuminate\Support\Facades\Route;

Route::post('validate', [ValidateKeyController::class, 'index'])
    ->middleware(['client:manage-key'])
    ->name('validation.index');
