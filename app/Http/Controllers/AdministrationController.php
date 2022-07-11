<?php

namespace App\Http\Controllers;

use App\Helpers\Products;
use App\Http\Resources\UserResource;
use App\Models\LicenceKey;
use App\Models\User;
use Illuminate\Http\Request;
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

        $licences = $user->licenceKeys()
            ->with('plan')
            ->get()
            ->map(function (LicenceKey $licence) use ($request): array {
                $price = app(Products::class)
                    ->getProductPrices(collect([$licence->plan->plan_id_on_paddle]), $request->user())
                    ->first();

                return [
                    'id' => $licence->id,
                    'plan' => [
                        'id' => $licence->plan->id,
                        'friendly_name' => $licence->plan->friendly_name,
                        'description' => $licence->plan->description,
                        'plan_name' => $licence->plan->plan_name,
                        'price' => $price['price'],
                        'frequency' => $price['frequency_name'],
                    ],
                    'key' => $licence->key,
                    'subscription_state' => $licence->subscription_state,
                    'valid_until_at' => $licence->valid_until_at_formatted,
                ];
            });

        return Inertia::render('Administration/Show', [
            'users' => new UserResource($user),
            'licences' => $licences,
        ]);
    }
}
