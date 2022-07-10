<?php

namespace App\Http\Controllers;

use App\Helpers\Products;
use App\Http\Requests\OfficeLifePriceRequest;
use App\Models\Plan;
use App\Services\UpdateSubscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OfficeLifeController extends Controller
{
    public const PRODUCT = 'OfficeLife';

    /**
     * Display OfficeLife licences.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function index(Request $request): Response
    {
        $subscription = $request->user()->subscriptions()
            ->active()
            ->product(static::PRODUCT)
            ->orderBy('created_at', 'desc')
            ->first();

        $plans = Plan::where('product', static::PRODUCT)->get();

        $productIds = $plans->pluck('plan_id_on_paddle');
        $prices = app(Products::class)->getProductPrices($productIds, $request->user(), optional($subscription)->quantity ?? 1);

        $plansCollection = $plans->map(function (Plan $plan) use ($prices, $subscription): array {
            $price = $prices->where('product_id', $plan->plan_id_on_paddle)->first();

            return [
                'id' => $plan->id,
                'friendly_name' => $plan->friendly_name,
                'description' => $plan->description,
                'plan_name' => $plan->plan_name,
                'single_price' => $price['price'],
                'price' => $price['price'],
                'frequency' => $price['frequency_name'],
                'quantity' => optional($subscription)->quantity ?? 1,
            ];
        });

        $licence = $request->user()->licenceKeys()
            ->where('plan_id', optional(optional($subscription)->plan)->id)
            ->orderBy('created_at', 'desc')
            ->first();

        return Inertia::render('OfficeLife/Index', [
            'plans' => $plansCollection,
            'current_licence' => $licence,
            'refresh' => $request->boolean('refresh'),
        ]);
    }

    /**
     * Get the updated price for the given plan.
     *
     * @param  \App\Http\Requests\OfficeLifePriceRequest  $request
     * @param  \App\Models\Plan  $plan
     * @return \Illuminate\Http\JsonResponse
     */
    public function price(OfficeLifePriceRequest $request, Plan $plan): JsonResponse
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
        ]);
    }

    /**
     * Subscribe to this plan.
     *
     * @param  \App\Http\Requests\OfficeLifePriceRequest  $request
     * @param  Plan  $plan
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function create(OfficeLifePriceRequest $request, Plan $plan)
    {
        if ($plan->product !== static::PRODUCT) {
            abort(401);
        }

        $url = $request->user()->newSubscription($plan->plan_name, $plan->plan_id_on_paddle)
            ->returnTo(route('officelife.index').'?refresh=true')
            ->quantity($request->quantity())
            ->create();

        return Inertia::location($url);
    }

    /**
     * Update subscription plan.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request): RedirectResponse
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
