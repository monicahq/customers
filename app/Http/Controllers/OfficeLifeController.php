<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class OfficeLifeController extends Controller
{
    public function index(Request $request)
    {
        $plans = Plan::where('product', 'OfficeLife')->get();

        $plansCollection = $plans->map(function (Plan $plan) use ($request): array {
            return [
                'id' => $plan->id,
                'friendly_name' => $plan->friendly_name,
                'description' => $plan->description,
                'plan_name' => $plan->plan_name,
                'single_price' => $plan->price,
                'price' => $plan->price,
                'frequency' => $plan->frequency,
                'quantity' => 0,
                'url' => [
                    'pay_link' => $request->user()->newSubscription($plan->plan_name, $plan->plan_id_on_paddle)
                        ->returnTo(route('officelife.index'))
                        ->create(),
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

    public function price(Request $request, Plan $plan)
    {
        if ($plan->product !== 'OfficeLife') {
            abort(404);
        }

        $quotedPrice = $plan->price * $request->input('quantity');

        return response()->json([
            'price' => $quotedPrice,
            'pay_link' => $request->user()->newSubscription($plan->plan_name, $plan->plan_id_on_paddle)
                ->returnTo(route('officelife.index'))
                ->quantity($request->input('quantity'))
                ->create(),
        ]);
    }
}
