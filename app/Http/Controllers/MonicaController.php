<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MonicaController extends Controller
{
    public const PRODUCT = 'Monica';

    public function index(Request $request)
    {
        $plans = Plan::where('product', static::PRODUCT)->get();

        $plansCollection = $plans->map(function (Plan $plan) use ($request): array {
            return [
                'id' => $plan->id,
                'friendly_name' => $plan->friendly_name,
                'description' => $plan->description,
                'plan_name' => $plan->plan_name,
                'price' => $plan->price,
                'frequency' => $plan->frequency,
                'url' => [
                    'pay_link' => $this->getPayLink($request, $plan),
                ],
            ];
        });

        $licence = $request->user()->licenceKeys()
            ->whereIn('plan_id', $plans->pluck('id'))
            ->orderBy('created_at', 'desc')
            ->first();

        return Inertia::render('Monica/Index', [
            'data' => [
                'plans' => $plansCollection,
                'current_licence' => $licence,
            ],
            'refresh' => $request->boolean('refresh'),
        ]);
    }

    private function getPayLink(Request $request, Plan $plan)
    {
        return $request->user()->newSubscription($plan->plan_name, $plan->plan_id_on_paddle)
            ->returnTo(route('monica.index').'?refresh=true')
            ->create();
    }
}
