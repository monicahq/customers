<?php

namespace App\Services;

use App\Models\User;
use App\Jobs\CreateAuditLog;
use App\Services\BaseService;
use App\Interfaces\ServiceInterface;
use App\Models\InstanceKey;
use App\Models\RelationshipGroupType;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class CreateInstanceKey extends BaseService
{
    private InstanceKey $instanceKey;
    private Collection $key;
    private User $user;
    private array $data;

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
            'max_number_of_employees' => 'required|integer',
        ];
    }

    /**
     * Create an instance key.
     *
     * @param  array  $data
     * @return InstanceKey
     */
    public function execute(array $data): InstanceKey
    {
        $this->validateRules($data);
        $this->data = $data;

        $this->user = User::findOrFail($data['user_id']);

        $this->generateKey();

        $this->encodeKey();

        return $this->instanceKey;
    }

    /**
     * The key is a json that contains:
     * - the validate until date
     * - the number of employees in the license,
     * - the company name,
     * - the email address of the user who purchased the license,
     *
     * @return void
     */
    private function generateKey(): void
    {
        $this->key = collect();
        $this->key->push([
            'user_email' => $this->user->email,
            'company' => $this->user->company_name,
            'valid_until_at' => Carbon::now()->addYear(),
            'max_number_of_employees' => $this->data['max_number_of_employees'],
            'private_instance_key' => config('customers.private_key_to_encrypt_instance_keys'),
        ]);
    }

    private function encodeKey(): void
    {
        $key = $this->key->toJson();
        $key = base64_encode($key);

        $this->instanceKey = InstanceKey::create([
            'plan_id' => $this->data['plan_id'],
            'user_id' => $this->user->id,
            'key' => $key,
            'valid_until_at' => Carbon::now()->addYear(),
        ]);
    }
}
