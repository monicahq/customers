<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class MonicaController extends Controller
{
    public function index(Request $request)
    {
        $plansCollection = Plan::where('product', 'Monica')->get()->map(function ($plan) use ($request) {
            return [
                'id' => $plan->id,
                'friendly_name' => $plan->friendly_name,
                'description' => $plan->description,
                'plan_name' => $plan->plan_name,
                'plan_id_on_paddle' => $plan->plan_id_on_paddle,
                'price' => $plan->price,
                'frequency' => $plan->frequency,
                'url' => [
                    'pay_link' => $request->user()->newSubscription($plan->plan_name, $plan->plan_id_on_paddle)
                        ->returnTo(route('monica.index'))
                        ->create(),
                ],
            ];
        });

        $licence = DB::table('licence_keys')
            ->join('plans', 'plans.id', '=', 'licence_keys.plan_id')
            ->select('licence_keys.id', 'licence_keys.paddle_cancel_url', 'licence_keys.key', 'licence_keys.paddle_update_url', 'licence_keys.subscription_state', 'plans.id', 'licence_keys.valid_until_at', 'plans.friendly_name', 'plans.description')
            ->where('plans.product', 'Monica')
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

        return Inertia::render('Monica/Index', [
            'data' => [
                'plans' => $plansCollection,
                'current_licence' => $currentLicence,
            ],
        ]);
    }
}
