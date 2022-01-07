<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Plan extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'friendly_name',
        'name_in_stripe',
        'price',
    ];

    /**
     * Get the keys values associated with the plan.
     *
     * @return HasMany
     */
    public function instanceKeys()
    {
        return $this->hasMany(InstanceKey::class);
    }
}
