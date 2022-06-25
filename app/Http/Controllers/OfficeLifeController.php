<?php

namespace App\Http\Controllers;

use App\Helpers\Products;
use App\Http\Requests\OfficeLifePriceRequest;
use App\Models\Plan;
use App\Services\UpdateSubscription;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OfficeLifeController extends Controller
{
    public const PRODUCT = 'OfficeLife';

    public function index(Request $request)
    {
        $subscription = $request->user()->subscriptions()
            ->active()
            ->notCancelled()
            ->product(static::PRODUCT)
            ->first();

        $plans = Plan::where('product', static::PRODUCT)->get();

        $productIds = $plans->pluck('plan_id_on_paddle');
        $prices = app(Products::class)->getProductPrices($productIds, $request->user(), $subscription !== null ? $subscription->quantity : 1);

        $plansCollection = $plans->map(function (Plan $plan) use ($request, $prices, $subscription): array {
            $price = $prices->where('product_id', $plan->plan_id_on_paddle)->first();

            return [
                'id' => $plan->id,
                'friendly_name' => $plan->friendly_name,
                'description' => $plan->description,
                'plan_name' => $plan->plan_name,
                'single_price' => $price['price'],
                'price' => $price['price'],
                'frequency' => $price['frequency_name'],
                'quantity' => $subscription !== null ? $subscription->quantity : 1,
                'url' => [
                    'pay_link' => $this->getPayLink($request, $plan),
                ],
            ];
        });

        $licence = $request->user()->licenceKeys()
            ->where('plan_id', optional($subscription)->plan->id)
            ->orderBy('created_at', 'desc')
            ->first();

        return Inertia::render('OfficeLife/Index', [
            'plans' => $plansCollection,
            'current_licence' => $licence,
            'refresh' => $request->boolean('refresh'),
        ]);
    }

    public function price(OfficeLifePriceRequest $request, Plan $plan)
    {
        if ($plan->product !== static::PRODUCT) {
            abort(401);
        }

        $plans = Plan::where('product', static::PRODUCT)->get();
        $productIds = $plans->pluck('plan_id_on_paddle');

        $price = app(Products::class)->getProductPrices($productIds, $request->user(), $request->quantity())
            ->where('product_id', $plan->plan_id_on_paddle)
            ->first();

        return response()->json([
            'price' => $price['price'],
            'pay_link' => $this->getPayLink($request, $plan, $request->quantity()),
        ]);
    }

    private function getPayLink(Request $request, Plan $plan, int $quantity = 1)
    {
        return $request->user()->newSubscription($plan->plan_name, $plan->plan_id_on_paddle)
            ->returnTo(route('officelife.index').'?refresh=true')
            ->quantity($quantity)
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
            'quantity' => $request->input('quantity'),
        ]);

        return back()->with([
            'refresh' => true,
        ]);
    }
}
