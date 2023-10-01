<?php

namespace App\Http\Controllers;

use App\Helpers\Products;
use App\Models\Plan;
use App\Services\UpdateSubscription;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Symfony\Component\HttpFoundation\Response;

class MonicaController extends BaseProductController
{
    public function productName(): string
    {
        return 'Monica';
    }

    /**
     * Display Monica licences.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function index(Request $request): InertiaResponse
    {
        $subscription = $this->subscription($request);

        $plans = Plan::where('product', $this->productName())->get();

        $productIds = $plans->pluck('plan_id_on_paddle');
        $prices = app(Products::class)->getProductPrices($productIds, $request->user());

        $plansCollection = $plans->map(function (Plan $plan) use ($prices): array {
            $price = $prices->where('product_id', $plan->plan_id_on_paddle)->first();

            return [
                'id' => $plan->id,
                'friendly_name' => __($plan->translation_key),
                'description' => __($plan->description_key),
                'plan_name' => $plan->plan_name,
                'price' => $price['price'],
                'frequency' => $price['frequency_name'],
            ];
        });

        $licence = $this->licence($request, $subscription);

        return Inertia::render('Monica/Index', [
            'plans' => $plansCollection,
            'current_licence' => $licence,
            'refresh' => $request->boolean('refresh'),
            'links' => [
                'billing' => Str::markdownExternalLink(__('Go to :url', [
                    'url' => config('customers.billing_links.monica'),
                ])),
                'paddle' =>  Str::markdownExternalLink(__('Secure payment by [Paddle](:url)', [
                    'url' => 'https://paddle.com',
                ])),
                'support' => Str::markdownExternalLink(__('If you experience issues after purchase, please contact us at support@monicahq.com.')),
            ],
        ]);
    }

    /**
     * Subscribe to this plan.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Plan  $plan
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function create(Request $request, Plan $plan): Response
    {
        if ($plan->product !== $this->productName()) {
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
