<?php

namespace Database\Seeders;

use App\Http\Controllers\MonicaController;
use App\Models\Plan;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $monica = app(MonicaController::class)->productName();
        $this->getOrCreatePlan($monica, 'monthly', trans_key('Monthly plan'), trans_key('Monica with all features'));
        $this->getOrCreatePlan($monica, 'yearly', trans_key('Yearly plan'), trans_key('Monica with all features'));
    }

    private function getOrCreatePlan(string $product, string $name, string $translation_key, string $description_key)
    {
        $plan = Plan::where('product', $product)
            ->where('plan_name', $name)
            ->first();

        if (! $plan) {
            $plan = Plan::create([
                'product' => $product,
                'plan_name' => $name,
                'translation_key' => $translation_key,
                'description_key' => $description_key,
                'plan_id_on_paddle' => 0,
            ]);
        }

        return $plan;
    }
}
