<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class AdministrationController extends Controller
{
    public function index(Request $request)
    {
        if (! Gate::allows('administration')) {
            abort(403);
        }

        $usersCollection = UserResource::collection(User::paginate(20));

        return Inertia::render('Administration/Index', [
            'users' => $usersCollection,
        ]);
    }

    public function show(Request $request, User $user)
    {
        if (! Gate::allows('administration')) {
            abort(403);
        }

        JsonResource::withoutWrapping();

        return Inertia::render('Administration/Show', [
            'user' => new UserResource($user),
        ]);
    }
}
