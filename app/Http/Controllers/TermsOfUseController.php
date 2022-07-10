<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class TermsOfUseController extends Controller
{
    public function index()
    {
        return Inertia::render('TermsOfService');
    }
}
