<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class PrivacyPolicyController extends Controller
{
    public function index()
    {
        return Inertia::render('PrivacyPolicy');
    }
}
