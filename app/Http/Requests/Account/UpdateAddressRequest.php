<?php

namespace App\Http\Requests\Account;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateAddressRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'address_line_1' => ['nullable', 'string'],
            'address_line_2' => ['nullable', 'string'],
            'city' => ['nullable', 'string'],
            'postal_code' => ['string', Rule::when(fn ($data) =>
                in_array(Str::of($data['country'])->upper(), [
                    //@see https://developer.paddle.com/reference/29717a4e58630-supported-countries#countries-requiring-postcode
                    'AU', 'CA', 'FR', 'DE', 'IN', 'IT', 'NL', 'ES', 'GB', 'US'
                ])
            , 'required', 'nullable')],
            'country' => ['nullable', 'string'],
            'state' => ['nullable', 'string'],
        ];
    }
}
