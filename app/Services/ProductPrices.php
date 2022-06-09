<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Laravel\Paddle\Cashier;
use Laravel\Paddle\ProductPrice;

class ProductPrices
{
    /**
     * Get the prices for a set of products for a given user.
     *
     * @param  \Illuminate\Support\Collection  $products
     * @param  \App\Models\User|null  $user
     * @param  int  $quantity
     * @return \Illuminate\Support\Collection
     */
    public function execute(Collection $products, ?User $user = null, int $quantity = 1): Collection
    {
        $country = $user !== null ? $user->paddleCountry() : 'US';

        return $this->getPrices($products, $country)
            ->map(function (array $price) use ($country, $quantity) {
                $price['price'] = collect($price['price'])
                    ->mapWithKeys(fn ($item, $key) => [$key => $item * $quantity])
                    ->toArray();
                $pprice = new ProductPrice($country, $price);

                return [
                    'product_id' => $price['product_id'],
                    'price' => $pprice->price()->gross(),
                    'currency' => $price['currency'],
                    'frequency' => $this->getFrequency($pprice),
                ];
            });
    }

    private function getPrices(Collection $products, string $country): Collection
    {
        $key = $this->getKey($country, $products);

        return Cache::remember($key, 60 * 60, function () use ($products, $country) {
            $prices = Cashier::productPrices($products->toArray(), [
                'customer_country' => $country,
            ]);

            return $prices->map(fn (ProductPrice $price) => $price->toArray());
        });
    }

    private function getKey(string $country, Collection $products): string
    {
        return App::getLocale().'|'.$country.'|'.$products->implode(',');
    }

    private function getFrequency(ProductPrice $price): ?string
    {
        $interval = $price->planInterval();
        $frequency = $price->planFrequency();

        switch ($interval) {
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
