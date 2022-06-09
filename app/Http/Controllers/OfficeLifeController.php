<?php

namespace App\Http\Controllers;

use App\Http\Requests\OfficeLifePriceRequest;
use App\Models\Plan;
use App\Services\ProductPrices;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OfficeLifeController extends Controller
{
    public const PRODUCT = 'OfficeLife';

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
                'single_price' => $price['price'],
                'price' => $price['price'],
                'frequency' => $price['frequency'],
                'quantity' => 1,
                'url' => [
                    'pay_link' => $this->getPayLink($request, $plan),
                    'price' => route('officelife.price', [
                        'plan' => $plan->id,
                    ]),
                ],
            ];
        });

        $licence = $request->user()->licenceKeys()
            ->whereIn('plan_id', $plans->pluck('id'))
            ->orderBy('created_at', 'desc')
            ->first();

        return Inertia::render('OfficeLife/Index', [
            'data' => [
                'plans' => $plansCollection,
                'current_licence' => $licence,
            ],
        ]);
    }

    public function price(OfficeLifePriceRequest $request, Plan $plan)
    {
        if ($plan->product !== static::PRODUCT) {
            abort(401);
        }

        $plans = Plan::where('product', static::PRODUCT)->get();
        $productIds = $plans->pluck('plan_id_on_paddle');

        $price = app(ProductPrices::class)->execute($productIds, $request->user(), $request->quantity())
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
            ->returnTo(route('officelife.index'))
            ->quantity($quantity)
            ->create();
    }
}
