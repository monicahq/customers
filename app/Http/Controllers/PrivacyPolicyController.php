<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Laravel\Paddle\Receipt;

class PrivacyPolicyController extends Controller
{
    public function index()
    {
        return Inertia::render('PrivacyPolicy');
    }
}
