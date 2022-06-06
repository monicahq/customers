<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Laravel\Paddle\ProductPrice;

class ProductPrices
{
    /**
     * Destroy a licence key based on the payload received by Paddle.
     * We react to the webhook `subscription_cancelled`.
     * We need to pass the payload as an array.
     *
     * @param  array  $payload
     * @return bool|null
     */
    public function execute(User $user, Collection $products): Collection
    {
        $key = $this->getKey($user, $products);

        return Cache::remember($key, 60 * 60, function () use ($user, $products) {
            $prices = $user->productPrices($products->toArray());

            return $prices->map(fn (ProductPrice $price) => [
                'product_id' => $price->product_id,
                'price' => $price->price()->gross(),
                'currency' => $price->currency,
            ]);
        });
    }

    private function getKey(User $user, Collection $products): string
    {
        return App::getLocale().'|'.$user->paddleCountry().'|'.$products->implode(',');
    }
}
