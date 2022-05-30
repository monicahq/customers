<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdministrationController extends Controller
{
    public function index(Request $request)
    {
        $usersCollection = User::get()->map(function (User $user): array {
            return [
                'id' => $user->id,
                'name' => $user->name,
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
