<?php

namespace App\Http\Controllers;

use App\Models\LicenceKey;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Inertia\Inertia;

abstract class BaseProductController extends Controller
{
    abstract function productName(): string;

    /**
     * Display Monica licences.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return Subscription|null
     */
    protected function subscription(Request $request): ?Subscription
    {
        return $request->user()->subscriptions()
            ->active()
            ->product($this->productName())
            ->orderBy('created_at', 'desc')
            ->first();
    }

    /**
     * Cancel paddle subscription.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Subscription  $subscription
     * @return LicenceKey|null
     */
    protected function licence(Request $request, ?Subscription $subscription): ?LicenceKey
    {
        return $request->user()->licenceKeys()
            ->where('plan_id', optional(optional($subscription)->plan)->id)
            ->orderBy('created_at', 'desc')
            ->first();
    }

    /**
     * Update paddle information.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function paddleUpdate(Request $request): Response
    {
        $subscription = $this->subscription($request);
        $licence = $this->licence($request, $subscription);

        if ($licence === null) {
            abort(401);
        }

        return Inertia::location($licence->paddle_update_url);
    }

    /**
     * Cancel paddle subscription.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function paddleCancel(Request $request): Response
    {
        $subscription = $this->subscription($request);
        $licence = $this->licence($request, $subscription);

        if ($licence === null) {
            abort(401);
        }

        return Inertia::location($licence->paddle_cancel_url);
    }
}
