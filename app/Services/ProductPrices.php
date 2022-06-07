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
     * Get the prices for a set of products for a given user.
     *
     * @param  \App\Models\User  $user
     * @param  \Illuminate\Support\Collection $products
     * @param  int  $quantity
     * @return bool|null
     */
    public function execute(User $user, Collection $products, int $quantity = 1): Collection
    {
        $key = $this->getKey($user, $products);

        $productPrices = Cache::remember($key, 60 * 60, function () use ($user, $products) {
            $prices = $user->productPrices($products->toArray());
            return $prices->map(fn (ProductPrice $price) => $price->toArray());
        });

        return $productPrices->map(function (array $price) use ($user, $quantity) {
            $price['price'] = collect($price['price'])->mapWithKeys(fn ($item, $key) => [$key => $item * $quantity])->toArray();
            $pprice = new ProductPrice($user->paddleCountry(), $price);

            return [
                'product_id' => $pprice->product_id,
                'price' => $pprice->price()->gross(),
                'currency' => $pprice->currency,
                'frequency' => $this->getFrequency($pprice),
            ];
        });
    }

    private function getKey(User $user, Collection $products): string
    {
        return App::getLocale().'|'.$user->paddleCountry().'|'.$products->implode(',');
    }

    private function getFrequency(ProductPrice $price): ?string
    {
        $interval = $price->planInterval();
        $frequency = $price->planFrequency();

        switch ($interval)
        {
            case 'day':
                return trans_choice('day|:period days', $frequency, ['period' => $frequency]);
            case 'week':
                return trans_choice('week|:period weeks', $frequency, ['period' => $frequency]);
            case 'month':
                return trans_choice('month|:period months', $frequency, ['period' => $frequency]);
            case 'year':
                return trans_choice('year|:period years', $frequency, ['period' => $frequency]);
            default:
                return null;
        }
    }
}
