<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ManageProduct;
use App\Http\Requests\OfficeLifePriceRequest;
use App\Models\Plan;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OfficeLifeController extends Controller
{
    use ManageProduct;

    public function productName(): string
    {
        return 'OfficeLife';
    }

    public function redirectTo(): string
    {
        return route('officelife.index');
    }

    public function index(Request $request)
    {
        $plans = Plan::where('product', $this->productName())->get();

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
        if ($plan->product !== $this->productName()) {
            abort(401);
        }

        $quotedPrice = $plan->price * $request->input('quantity');

        return response()->json([
            'price' => $quotedPrice,
            'pay_link' => $this->getPayLink($request, $plan, $request->input('quantity')),
        ]);
    }
}
