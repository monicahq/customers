<?php

namespace App\Services;

use App\Models\LicenceKey;
use App\Models\User;

class RenewLicenceKey extends BaseService
{
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
        ];
    }

    /**
     * Renew a licence key.
     *
     * @param  array  $data
     * @return bool
     */
    public function execute(array $data): bool
    {
        $this->validateRules($data);
        $this->data = $data;

        $this->licenceKey = LicenceKey::where('key', $this->data['licence_key'])
            ->firstOrFail();

        if ($this->licenceKey->valid_until_at->isPast()) {
            throw new \Exception('Licence key has expired.');
        }

        return true;
    }
}
