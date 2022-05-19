<?php

namespace App\Actions\Fortify;

use Illuminate\Support\Facades\App;
use Laravel\Fortify\Rules\Password;

trait PasswordValidationRules
{
    /**
     * Get the validation rules used to validate passwords.
     *
     * @return array
     */
    protected function passwordRules()
    {
        $rules = new Password;

        if (App::environment('production')) {
            $rules->length(8)
                ->requireUppercase()
                ->requireNumeric();
        }

        return [
            'required',
            'string',
            'confirmed',
            $rules
        ];
    }
}
