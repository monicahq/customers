<?php

namespace Tests\Unit\Services;

use Exception;
use Carbon\Carbon;
use Tests\TestCase;
use App\Models\LicenceKey;
use App\Services\ValidateLicenceKey;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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

    /** @test */
    public function it_fails_if_the_key_doesnt_exist(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $request = [
            'licence_key' => '123',
        ];

        (new ValidateLicenceKey)->execute($request);
    }

    /** @test */
    public function it_fails_if_the_key_is_not_valid_anymore(): void
    {
        Carbon::setTestNow(Carbon::create(2018, 1, 1));

        $this->expectException(Exception::class);

        $licenceKey = LicenceKey::factory()->create([
            'valid_until_at' => '2017-01-01',
        ]);

        $request = [
            'licence_key' => $licenceKey->key,
        ];

        (new ValidateLicenceKey)->execute($request);
    }
}
