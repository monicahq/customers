<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class AdministrationController extends Controller
{
    public function index(Request $request)
    {
        $usersCollection = User::get()->map(function (User $user) use ($request): array {
            return [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'url' => [
                    'show' => route('administration.user.show', [
                        'user' => $user->id,
                    ]),
                ],
            ];
        });

        return Inertia::render('Administration/Index', [
            'data' => [
                'users' => $usersCollection,
            ],
        ]);
    }

    public function show(Request $request, int $userId)
    {
        return Inertia::render('Administration/Show');
    }
}
