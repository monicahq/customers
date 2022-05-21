<?php

namespace Tests\Unit\Services;

use App\Exceptions\PastDueLicenceException;
use App\Models\LicenceKey;
use App\Services\ValidateLicenceKey;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\TestCase;

class ValidateLicenceKeyTest extends TestCase
{
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

        $this->assertEquals(Carbon::parse('2019-01-01'), $response);
    }

    /** @test */
    public function it_fails_if_the_key_is_not_valid(): void
    {
        $this->expectException(\Illuminate\Contracts\Encryption\DecryptException::class);

        $request = [
            'licence_key' => '123',
        ];

        (new ValidateLicenceKey)->execute($request);
    }

    /** @test */
    public function it_fails_if_the_key_doesnt_exist(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $request = [
            'licence_key' => app('license.encrypter')->encrypt(['test']),
        ];

        (new ValidateLicenceKey)->execute($request);
    }

    /** @test */
    public function it_fails_if_the_key_is_not_valid_anymore(): void
    {
        Carbon::setTestNow(Carbon::create(2018, 1, 1));

        $this->expectException(PastDueLicenceException::class);

        $licenceKey = LicenceKey::factory()->create([
            'valid_until_at' => '2017-01-01',
        ]);

        $request = [
            'licence_key' => $licenceKey->key,
        ];

        (new ValidateLicenceKey)->execute($request);
    }

    /** @test */
    public function it_fails_if_the_subscription_has_been_canceled(): void
    {
        Carbon::setTestNow(Carbon::create(2018, 1, 1));

        $this->expectException(PastDueLicenceException::class);

        $licenceKey = LicenceKey::factory()->create([
            'valid_until_at' => '2019-01-01',
            'subscription_state' => 'subscription_canceled',
        ]);

        $request = [
            'licence_key' => $licenceKey->key,
        ];

        (new ValidateLicenceKey)->execute($request);
    }
}
