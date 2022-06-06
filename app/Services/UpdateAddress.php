<?php

namespace App\Services;

use App\Models\User;

class UpdateAddress extends BaseService
{
    /**
     * Get the validation rules that apply to the service.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'address_line_1' => ['nullable', 'string'],
            'address_line_2' => ['nullable', 'string'],
            'city' => ['nullable', 'string'],
            'postal_code' => ['nullable', 'string'],
            'country' => ['nullable', 'string'],
            'state' => ['nullable', 'string'],
        ];
    }

    /**
     * Update user address.
     *
     * @param  array  $data
     * @return User
     */
    public function execute(array $data): User
    {
        $this->validateRules($data);

        $user = User::findOrFail($data['user_id']);

        $user->address_line_1 = $this->valueOrNull($data, 'address_line_1');
        $user->address_line_2 = $this->valueOrNull($data, 'address_line_2');
        $user->city = $this->valueOrNull($data, 'city');
        $user->postal_code = $this->valueOrNull($data, 'postal_code');
        $user->country = $this->valueOrNull($data, 'country');
        $user->state = $this->valueOrNull($data, 'state');

        $user->save();

        return $user;
    }
}
