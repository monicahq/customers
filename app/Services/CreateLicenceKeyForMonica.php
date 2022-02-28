<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Plan;
use App\Models\User;
use App\Models\LicenceKey;
use Illuminate\Support\Collection;

class CreateLicenceKeyForMonica extends BaseService
{
    private LicenceKey $licenceKey;
    private Collection $key;
    private User $user;
    private Plan $plan;
    private array $data;
    private Carbon $nextDate;

    /**
     * Get the validation rules that apply to the service.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'user_id' => 'required|integer|exists:users,id',
            'plan_id' => 'required|integer|exists:plans,id',
        ];
    }

    /**
     * Create an instance key.
     *
     * @param  array  $data
     * @return LicenceKey
     */
    public function execute(array $data): LicenceKey
    {
        $this->validateRules($data);
        $this->data = $data;

        $this->user = User::findOrFail($data['user_id']);
        $this->plan = Plan::findOrFail($data['plan_id']);

        $this->calculateNextDate();
        $this->generateKey();
        $this->encodeKey();

        return $this->licenceKey;
    }

    private function calculateNextDate(): void
    {
        if ($this->plan->frequency == Plan::TYPE_MONTHLY) {
            $this->nextDate = Carbon::now()->addMonth();
        } else {
            $this->nextDate = Carbon::now()->addYear();
        }
    }

    /**
     * The key is a json that contains:
     * - frequency: monthly|yearly
     * - number of max users,
     * - the date the next check should occured,
     * - the email address of the user who purchased the license.
     *
     * @return void
     */
    private function generateKey(): void
    {
        $this->key = collect();

        $this->key->push([
            'frequency' => $this->plan->frequency,
            'purchaser_email' => $this->user->email,
            'next_check_at' => $this->nextDate,
        ]);
    }

    private function encodeKey(): void
    {
        $key = $this->key->toJson();
        $key = base64_encode($key.config('customers.private_key_to_encrypt_licence_keys'));

        $this->licenceKey = LicenceKey::create([
            'plan_id' => $this->data['plan_id'],
            'user_id' => $this->user->id,
            'key' => $key,
            'valid_until_at' => $this->nextDate,
        ]);
    }
}
