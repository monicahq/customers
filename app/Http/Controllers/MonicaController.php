<?php

namespace App\Http\Controllers;

use App\Helpers\Products;
use App\Models\Plan;
use App\Services\UpdateSubscription;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MonicaController extends Controller
{
    public const PRODUCT = 'Monica';

    /**
     * Display Monica licences.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        $plans = Plan::where('product', static::PRODUCT)->get();

        $productIds = $plans->pluck('plan_id_on_paddle');
        $prices = app(Products::class)->getProductPrices($productIds, $request->user());

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

        $subscription = $request->user()->subscriptions()
            ->active()
            ->notCancelled()
            ->product(static::PRODUCT)
            ->first();

        $licence = $request->user()->licenceKeys()
            ->where('plan_id', optional($subscription)->plan->id)
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

    /**
     * Update Monica licences.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function update(Request $request)
    {
        app(UpdateSubscription::class)->execute([
            'user_id' => $request->user()->id,
            'plan_id' => $request->input('plan_id'),
            'product' => static::PRODUCT,
        ]);

        return back()->with([
            'refresh' => true,
        ]);
    }
}
