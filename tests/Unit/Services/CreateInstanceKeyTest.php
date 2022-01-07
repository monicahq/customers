<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\User;
use App\Models\Account;
use App\Models\InstanceKey;
use App\Models\Plan;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\ValidationException;
use App\Services\User\StoreNameOrderPreference;
use App\Services\Account\ManageLabels\CreateLabel;
use App\Services\CreateInstanceKey;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CreateInstanceKeyTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_creates_an_instance_key(): void
    {
        Carbon::setTestNow(Carbon::create(2018, 1, 1));

        $user = User::factory()->create();
        $plan = Plan::factory()->create();

        $request = [
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'max_number_of_employees' => 10,
        ];

        $instanceKey = (new CreateInstanceKey)->execute($request);

        $this->assertInstanceOf(InstanceKey::class, $instanceKey);
        $this->assertIsString($instanceKey->key);

        $array = json_decode(base64_decode($instanceKey->key), true);

        $this->assertArrayHasKey('user_email', $array[0]);
        $this->assertArrayHasKey('company', $array[0]);
        $this->assertArrayHasKey('valid_until_at', $array[0]);
        $this->assertArrayHasKey('max_number_of_employees', $array[0]);

        $this->assertEquals(
            $user->email,
            $array[0]['user_email']
        );
        $this->assertEquals(
            $user->company_name,
            $array[0]['company']
        );
        $this->assertEquals(
            '2019-01-01T00:00:00.000000Z',
            $array[0]['valid_until_at']
        );
        $this->assertEquals(
            10,
            $array[0]['max_number_of_employees']
        );

        $this->assertDatabaseHas('instance_keys', [
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'key' => $instanceKey->key,
        ]);
    }
}
