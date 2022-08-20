<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Paddle\Billable;
use Laravel\Sanctum\HasApiTokens;
use LaravelWebauthn\WebauthnAuthenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    use Billable;
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use WebauthnAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'company_name',
        'total_number_of_employees',
        'address_line_1',
        'address_line_2',
        'city',
        'postal_code',
        'country',
        'state',
        'instance_administrator',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'instance_administrator' => 'boolean',
    ];

    /**
     * Get the licence key associated with the user.
     *
     * @return HasMany
     */
    public function licenceKeys()
    {
        return $this->hasMany(LicenceKey::class);
    }

    /**
     * Get the user tokens for external login providers.
     *
     * @return HasMany
     */
    public function userTokens()
    {
        return $this->hasMany(UserToken::class);
    }

    /**
     * Get the billable model's country to associate with Paddle.
     *
     * This needs to be a 2 letter code. See the link below for supported countries.
     *
     * @return string|null
     *
     * @link https://developer.paddle.com/reference/platform-parameters/supported-countries
     */
    public function paddleCountry(): ?string
    {
        return $this->country ?? config('customers.fallback_country');
    }

    /**
     * Get the billable model's postcode to associate with Paddle.
     *
     * See the link below for countries which require this.
     *
     * @return string|null
     *
     * @link https://developer.paddle.com/reference/platform-parameters/supported-countries#countries-requiring-postcode
     */
    public function paddlePostcode(): ?string
    {
        return $this->postal_code;
    }

    /**
     * Get the created at date formatted.
     *
     * @return string|null
     */
    public function getCreatedAtFormattedAttribute(): ?string
    {
        return $this->created_at->isoFormat('LL');
    }
}
