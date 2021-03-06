<?php

namespace App\Services;

use App\Exceptions\PastDueLicenceException;
use App\Models\LicenceKey;
use Carbon\Carbon;

class ValidateLicenceKey extends BaseService
{
    private LicenceKey $licenceKey;
    private array $data;

    /**
     * Get the validation rules that apply to the service.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'licence_key' => 'required|string',
        ];
    }

    /**
     * Validate a licence key.
     *
     * @param  array  $data
     * @return Carbon
     *
     * @throws \App\Exceptions\PastDueLicenceException
     */
    public function execute(array $data): Carbon
    {
        $this->validateRules($data);
        $this->data = $data;

        $this->decodeKey();

        $this->licenceKey = LicenceKey::where('key', $this->data['licence_key'])
            ->firstOrFail();

        if ($this->licenceKey->valid_until_at->isPast() ||
            $this->licenceKey->subscription_state === 'subscription_canceled') {
            throw new PastDueLicenceException;
        }

        return $this->licenceKey->valid_until_at;
    }

    private function decodeKey(): array
    {
        $encrypter = app('license.encrypter');

        return $encrypter->decrypt($this->data['licence_key']);
    }
}
