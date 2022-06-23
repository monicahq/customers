<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Inertia\Inertia;
use Inertia\Response;

class AccountController extends Controller
{
    /**
     * Show user account.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function show(Request $request): Response
    {
        $countries = App::make('countrylist')->getList(App::getLocale());

        $countries = collect($countries)->map(fn ($item, $key) => [
            'id' => $key,
            'name' => $item,
        ]);

        return Inertia::render('Account/Show', [
            'countries' => $countries->values(),
        ]);
    }
}
