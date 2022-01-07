<?php

namespace App\Services;

use App\Models\User;
use App\Models\Vault;
use App\Models\Account;
use App\Models\Contact;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\NotEnoughPermissionException;

abstract class BaseService
{
    /**
     * Get the validation rules that apply to the service.
     *
     * @return array
     */
    public function rules(): array
    {
        return [];
    }

    /**
     * Validate an array against a set of rules.
     *
     * @param  array  $data
     * @return bool
     */
    public function validateRules(array $data): bool
    {
        $validator = Validator::make($data, $this->rules())->validate();

        return true;
    }

    /**
     * Returns the value if it's defined, or false otherwise.
     *
     * @param  mixed  $data
     * @param  mixed  $index
     * @return mixed
     */
    public function valueOrFalse($data, $index)
    {
        if (empty($data[$index])) {
            return false;
        }

        return $data[$index];
    }

    /**
     * Checks if the value is empty or null.
     *
     * @param  mixed  $data
     * @param  mixed  $index
     * @return mixed
     */
    public function valueOrNull($data, $index)
    {
        if (empty($data[$index])) {
            return;
        }

        return $data[$index] == '' ? null : $data[$index];
    }
}
