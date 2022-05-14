<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    use HasFactory;

    /**
     * Possible type.
     */
    const TYPE_MONTHLY = 'monthly';
    const TYPE_YEARLY = 'yearly';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'id',
        'product',
        'friendly_name',
        'description',
        'plan_name',
        'plan_id_on_paddle',
        'price',
        'frequency',
    ];

    /**
     * Get the keys values associated with the plan.
     *
     * @return HasMany
     */
    public function licenceKeys()
    {
        return $this->hasMany(LicenceKey::class);
    }
}
