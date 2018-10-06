<?php

namespace TheRealDb\ShopifyAuth\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use TheRealDb\ShopifyAuth\Http\Models\ShopifyCharge;

class ShopifyShop extends Model
{
	use SoftDeletes;

	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['domain'];

    /**
	 * Determine if the store is on trial.
	 *
	 * @param  string  $subscription
	 * @param  string|null  $plan
	 * @return bool
	 */
	public function onTrial($subscription = 'default', $plan = null)
	{
	    if (func_num_args() === 0 && $this->onGenericTrial()) {
	        return true;
	    }

	    $subscription = $this->subscription($subscription);

	    if (is_null($plan)) {
	        return $subscription && $subscription->onTrial();
	    }

	    return $subscription && $subscription->onTrial() && $subscription->stripe_plan === $plan;
	}

	/**
	 * Determine if the store is on a "generic" trial at the model level.
	 *
	 * @return bool
	 */
	public function onGenericTrial()
	{
	    return $this->trial_ends_at && Carbon\Carbon::now()->lt($this->trial_ends_at);
	}

	/**
	 * Determine if the store has a given subscription.
	 *
	 * @param  string  $subscription
	 * @param  string|null  $plan
	 * @return bool
	 */
	public function subscribed($subscription = 'default', $plan = null)
	{
	    $subscription = $this->subscription($subscription);

	    if (is_null($subscription)) {
	        return false;
	    }

	    if (is_null($plan)) {
	        return $subscription->valid();
	    }

	    return $subscription->valid() && $subscription->shopify_plan === $plan;
	}

	/**
	 * Get a subscription instance by name.
	 *
	 * @param  string  $subscription
	 * @return TheRealDb\ShopifyAuth\Http\Models\ShopifyCharge|null
	 */
	public function subscription($subscription = 'default')
	{
	    return $this->subscriptions->sortByDesc(function ($value) {
	        return $value->created_at->getTimestamp();
	    })
	    ->first(function ($value) use ($subscription) {
	        return $value->name === $subscription;
	    });
	}

	/**
	 * Get all of the subscriptions for the store.
	 *
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	public function subscriptions()
	{
	    return $this->hasMany('TheRealDb\ShopifyAuth\Http\Models\ShopifyCharge')
	        ->where('charge_type', ShopifyCharge::CHARGE_RECURRING)
	        ->orderBy('created_at', 'desc');
	}
}
