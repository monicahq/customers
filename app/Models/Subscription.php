<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use Laravel\Paddle\Subscription as CashierSubscription;

class Subscription extends CashierSubscription
{
    use HasFactory;

    /**
     * Get the associated plan.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function plan()
    {
        return $this->hasOne(Plan::class, 'plan_id_on_paddle', 'paddle_plan');
    }

    /**
     * Scope a query to only include subscription on this product.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeProduct($query, string $product)
    {
        return $query
        ->whereIn('paddle_plan', function ($query) use ($product) {
            $query->select('plan_id_on_paddle')
                  ->from('plans')
                  ->where('plans.product', $product);
        });
    }
}
