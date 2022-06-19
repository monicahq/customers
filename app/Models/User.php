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
use LaravelWebauthn\Models\WebauthnKey;

class User extends Authenticatable implements MustVerifyEmail
{
    use Billable;
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use TwoFactorAuthenticatable;

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
     * Get the webauthn keys associated to this user.
     *
     * @return HasMany
     */
    public function webauthnKeys()
    {
        return $this->hasMany(WebauthnKey::class);
    }
}
