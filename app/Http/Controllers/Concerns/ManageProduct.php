<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Plan;
use Illuminate\Http\Request;

trait ManageProduct
{
    abstract public function productName(): string;

    /**
     * Get a new subscription link.
     *
     * @param  Reqest  $request
     * @param  Plan  $plan
     * @param  int  $quantity
     * @return string
     */
    public function getPayLink(Request $request, Plan $plan, int $quantity = 1): string
    {
        return $request->user()->newSubscription($plan->plan_name, $plan->plan_id_on_paddle)
            ->returnTo($this->redirectPath())
            ->quantity($quantity)
            ->create();
    }

    /**
     * Get the post subscription redirect path.
     *
     * @return string
     */
    public function redirectPath()
    {
        if (method_exists($this, 'redirectTo')) {
            return $this->redirectTo();
        }

        return property_exists($this, 'redirectTo') ? $this->redirectTo : '/home';
    }
}
