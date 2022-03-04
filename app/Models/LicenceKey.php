<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LicenceKey extends Model
{
    use HasFactory;

    protected $table = 'licence_keys';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
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
     * @var array
     */
    protected $dates = [
        'valid_until_at',
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
}
