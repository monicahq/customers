<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Services\ProductPrices;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MonicaController extends Controller
{
    public const PRODUCT = 'Monica';

    public function index(Request $request)
    {
        $plans = Plan::where('product', static::PRODUCT)->get();

        $productIds = $plans->pluck('plan_id_on_paddle');
        $prices = app(ProductPrices::class)->execute($productIds, $request->user());

        $plansCollection = $plans->map(function (Plan $plan) use ($request, $prices): array {
            $price = $prices->where('product_id', $plan->plan_id_on_paddle)->first();

            return [
                'id' => $plan->id,
                'friendly_name' => $plan->friendly_name,
                'description' => $plan->description,
                'plan_name' => $plan->plan_name,
                'price' => $price['price'],
                'frequency' => $price['frequency_name'],
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
