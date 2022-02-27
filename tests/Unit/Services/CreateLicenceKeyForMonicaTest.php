<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\User;
use App\Models\Account;
use App\Models\InstanceKey;
use App\Models\LicenceKey;
use App\Models\Plan;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\ValidationException;
use App\Services\User\StoreNameOrderPreference;
use App\Services\Account\ManageLabels\CreateLabel;
use App\Services\CreateInstanceKey;
use App\Services\CreateLicenceKeyForMonica;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CreateLicenceKeyForMonicaTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_creates_an_instance_key(): void
    {
        Carbon::setTestNow(Carbon::create(2018, 1, 1));
        config(['customers.private_key_to_encrypt_licence_keys' => '123']);

        $user = User::factory()->create();
        $plan = Plan::factory()->create([
            'frequency' => Plan::TYPE_MONTHLY,
        ]);

        $request = [
            'user_id' => $user->id,
            'plan_id' => $plan->id,
        ];

        $licenceKey = (new CreateLicenceKeyForMonica)->execute($request);

        $this->assertInstanceOf(LicenceKey::class, $licenceKey);
        $this->assertIsString($licenceKey->key);

        $licenceKey = $licenceKey.'123';
        $array = json_decode(base64_decode($licenceKey), true);

        $this->assertArrayHasKey('purchaser_email', $array[0]);
        $this->assertArrayHasKey('frequency', $array[0]);
        $this->assertArrayHasKey('next_check_at', $array[0]);

        $this->assertEquals(
            $user->email,
            $array[0]['purchaser_email']
        );
        $this->assertEquals(
            '2018-02-01T00:00:00.000000Z',
            $array[0]['next_check_at']
        );
        $this->assertEquals(
            Plan::TYPE_MONTHLY,
            $array[0]['frequency']
        );

        $this->assertDatabaseHas('licence_keys', [
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'key' => $licenceKey['key'],
            'valid_until_at' => '2018-02-01T00:00:00.000000Z',
        ]);
    }
}
