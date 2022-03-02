<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Inertia\Inertia;
use Illuminate\Http\Request;

class MonicaController extends Controller
{
    public function index(Request $request)
    {
        $plansCollection = Plan::all()->map(function ($plan) use ($request) {
            return [
                'id' => $plan->id,
                'friendly_name' => $plan->friendly_name,
                'description' => $plan->description,
                'plan_name' => $plan->plan_name,
                'plan_id_on_paddle' => $plan->plan_id_on_paddle,
                'price' => $plan->price,
                'frequency' => $plan->frequency,
                'pay_link' => $request->user()->newSubscription($plan->plan_name, $premium = $plan->plan_id_on_paddle)
                    ->returnTo(route('monica.index'))
                    ->create(),
            ];
        });

        return Inertia::render('Monica/Index', [
            'data' => $plansCollection,
        ]);
    }
}
