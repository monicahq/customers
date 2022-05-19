<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Paddle\Billable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, Billable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'email_verified_at',
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
     * Get the customer's email address to associate with Paddle.
     *
     * @return string|null
     */
    public function paddleEmail(): ?string
    {
        return $this->email;
    }
}
