<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LicenceKey extends Model
{
    use HasFactory;

    protected $table = 'licence_keys';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'plan_id',
        'user_id',
        'key',
        'valid_until_at',
        'subscription_state',
        'paddle_cancel_url',
        'paddle_update_url',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array<string>
     */
    protected $dates = [
        'valid_until_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'valid_until_at' => 'date:Y-m-d',
    ];

    /**
     * The attributes that should be visible in serialization.
     *
     * @var array<int,string>
     */
    protected $visible = [
        'id',
        'plan_id',
        'key',
        'subscription_state',
        'valid_until_at',
        'valid_until_at_formatted',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<string>
     */
    protected $appends = [
        'valid_until_at_formatted',
    ];

    /**
     * Get the user associated with the licence key.
     *
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the plan associated with the licence key.
     *
     * @return BelongsTo
     */
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Get the new validation date formatted.
     *
     * @return string|null
     */
    public function getValidUntilAtFormattedAttribute(): ?string
    {
        return $this->valid_until_at->isoFormat('LL');
    }
}
