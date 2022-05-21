<?php

namespace App\Http\Controllers;

use App\Http\Requests\OfficeLifePriceRequest;
use App\Models\Plan;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OfficeLifeController extends Controller
{
    public const PRODUCT = 'OfficeLife';

    public function index(Request $request)
    {
        $plans = Plan::where('product', static::PRODUCT)->get();

        $plansCollection = $plans->map(function (Plan $plan) use ($request): array {
            return [
                'id' => $plan->id,
                'friendly_name' => $plan->friendly_name,
                'description' => $plan->description,
                'plan_name' => $plan->plan_name,
                'single_price' => $plan->price,
                'price' => $plan->price,
                'frequency' => $plan->frequency,
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
            abort(404);
        }

        $quotedPrice = $plan->price * $request->input('quantity');

        return response()->json([
            'price' => $quotedPrice,
            'pay_link' => $this->getPayLink($request, $plan, $request->input('quantity')),
        ]);
    }

    private function getPayLink(Request $request, Plan $plan, int $quantity = 1)
    {
        if (! config('cashier.vendor_id') || ! config('cashier.vendor_auth_code')) {
            return null;
        }

        // @codeCoverageIgnoreStart
        return $request->user()->newSubscription($plan->plan_name, $plan->plan_id_on_paddle)
            ->returnTo(route('officelife.index'))
            ->quantity($quantity)
            ->create();
        // @codeCoverageIgnoreEnd
    }
}
