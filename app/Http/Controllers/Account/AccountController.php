<?php

namespace App\Http\Controllers\Account;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Inertia\Inertia;

class AccountController extends Controller
{
    /**
     * Show user account.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $driver
     * @return \Illuminate\Http\RedirectResponse
     */
    public function show(Request $request)
    {
        return Inertia::render('Account/Show');
    }
}
