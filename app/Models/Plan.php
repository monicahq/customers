<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'id',
        'product',
        'translation_key',
        'description_key',
        'plan_name',
        'plan_id_on_paddle',
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
