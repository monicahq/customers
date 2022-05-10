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
        $plansCollection = Plan::where('product', 'OfficeLife')->get()->map(function ($plan) use ($request) {
            return [
                'id' => $plan->id,
                'friendly_name' => $plan->friendly_name,
                'description' => $plan->description,
                'plan_name' => $plan->plan_name,
                'plan_id_on_paddle' => $plan->plan_id_on_paddle,
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

        $licence = DB::table('licence_keys')
            ->join('plans', 'plans.id', '=', 'licence_keys.plan_id')
            ->select('licence_keys.id', 'licence_keys.paddle_cancel_url', 'licence_keys.key', 'licence_keys.paddle_update_url', 'licence_keys.subscription_state', 'plans.id', 'licence_keys.valid_until_at', 'plans.friendly_name', 'plans.description')
            ->where('plans.product', 'OfficeLife')
            ->orderBy('licence_keys.created_at', 'desc')
            ->first();

        $currentLicence = null;
        if ($licence) {
            $currentLicence = [
                'id' => $licence->id,
                'key' => $licence->key,
                'paddle_cancel_url' => $licence->paddle_cancel_url,
                'paddle_update_url' => $licence->paddle_update_url,
                'subscription_state' => $licence->subscription_state,
                'valid_until_at' => Carbon::parse($licence->valid_until_at)->format('Y-m-d'),
            ];
        }

        return Inertia::render('OfficeLife/Index', [
            'data' => [
                'plans' => $plansCollection,
                'current_licence' => $currentLicence,
            ],
        ]);
    }

    public function price(Request $request, int $planId)
    {
        $plan = Plan::findOrFail($planId);

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
