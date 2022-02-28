<?php

namespace App\Services;

use App\Models\LicenceKey;

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
