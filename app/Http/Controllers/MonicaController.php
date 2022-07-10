<?php

namespace App\Http\Controllers;

use App\Helpers\Products;
use App\Models\Plan;
use App\Services\UpdateSubscription;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MonicaController extends Controller
{
    public const PRODUCT = 'Monica';

    /**
     * Display Monica licences.
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
        $prices = app(Products::class)->getProductPrices($productIds, $request->user());

        $plansCollection = $plans->map(function (Plan $plan) use ($prices): array {
            $price = $prices->where('product_id', $plan->plan_id_on_paddle)->first();

            return [
                'id' => $plan->id,
                'friendly_name' => $plan->friendly_name,
                'description' => $plan->description,
                'plan_name' => $plan->plan_name,
                'price' => $price['price'],
                'frequency' => $price['frequency_name'],
            ];
        });

        $licence = $request->user()->licenceKeys()
            ->where('plan_id', optional(optional($subscription)->plan)->id)
            ->orderBy('created_at', 'desc')
            ->first();

        return Inertia::render('Monica/Index', [
            'plans' => $plansCollection,
            'current_licence' => $licence,
            'refresh' => $request->boolean('refresh'),
        ]);
    }

    /**
     * Subscribe to this plan.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Plan  $plan
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function create(Request $request, Plan $plan)
    {
        if ($plan->product !== static::PRODUCT) {
            abort(401);
        }

        $url = $request->user()->newSubscription($plan->plan_name, $plan->plan_id_on_paddle)
            ->returnTo(route('monica.index').'?refresh=true')
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
        ]);

        return back()->with([
            'refresh' => true,
        ]);
    }
}
