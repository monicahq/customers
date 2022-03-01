<?php

namespace Tests\Unit\Services;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\Plan;
use App\Models\User;
use App\Models\LicenceKey;
use Illuminate\Support\Str;
use App\Services\CreateLicenceKeyForMonica;
use App\Services\ValidateLicenceKey;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ValidateLicenceKeyTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_validates_an_existing_key(): void
    {
        Carbon::setTestNow(Carbon::create(2018, 1, 1));

        $licenceKey = LicenceKey::factory()->create([
            'valid_until_at' => '2019-01-01',
        ]);

        $request = [
            'licence_key' => $licenceKey->key,
        ];

        $response = (new ValidateLicenceKey)->execute($request);

        $this->assertTrue($response);
    }
}
