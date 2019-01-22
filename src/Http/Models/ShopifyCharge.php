<?php

namespace TheRealDb\ShopifyAuth\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class ShopifyCharge extends Model
{
    use SoftDeletes;

    /**
     * Types of charges
     */
    const CHARGE_RECURRING = 1;
    const CHARGE_ONETIME = 2;
    const CHARGE_USAGE = 3;
    const CHARGE_CREDIT = 4;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'trial_ends_at', 'ends_at', 'created_at', 'updated_at', 'deleted_at'
    ];

    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $fillable = ['shopify_shop_id', 'charge_id', 'name', 'plan', 'plan_id', 'quantity', 'charge_type', 'trial_ends_at', 'ends_at'];

    /**
     * Gets the shop for the charge.
     *
     * @return TheRealDb\ShopifyAuth\Http\Models\ShopifyShop
     */
    public function shop()
    {
        return $this->belongsTo('TheRealDb\ShopifyAuth\Http\Models\ShopifyShop');
    }

    /**
     * Checks if the charge is a test.
     *
     * @return bool
     */
    public function isTest()
    {
        return (bool) $this->test;
    }

    /**
     * Determine if the subscription is active, on trial, or within its grace period.
     *
     * @return bool
     */
    public function valid()
    {
        return $this->active() || $this->onTrial() || $this->onGracePeriod();
    }

    /**
     * Determine if the subscription is active.
     *
     * @return bool
     */
    public function active()
    {
        return is_null($this->ends_at) || $this->onGracePeriod();
    }

    /**
     * Determine if the subscription is no longer active.
     *
     * @return bool
     */
    public function cancelled()
    {
        return ! is_null($this->ends_at);
    }

    /**
     * Determine if the subscription is within its trial period.
     *
     * @return bool
     */
    public function onTrial()
    {
        if (! is_null($this->trial_ends_at)) {
            return Carbon::now()->lt($this->trial_ends_at);
        } else {
            return false;
        }
    }

    /**
     * Determine if the subscription is within its grace period after cancellation.
     *
     * @return bool
     */
    public function onGracePeriod()
    {
        if (! is_null($endsAt = $this->ends_at)) {
            return Carbon::now()->lt(Carbon::instance($endsAt));
        } else {
            return false;
        }
    }
}
