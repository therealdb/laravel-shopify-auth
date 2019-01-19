<?php

namespace TheRealDb\ShopifyAuth\Http\Middleware;

use Closure;
use Shopify;
use ShopifyBilling;
use \Carbon\Carbon;

/* Models */
use TheRealDb\ShopifyAuth\Http\Models\ShopifyShop;
use TheRealDb\ShopifyAuth\Http\Models\ShopifyPlan;

/* Traits */
use TheRealDb\ShopifyAuth\Http\Traits\ShopifyAuthTrait;

class ShopifyShopCharge
{
    use ShopifyAuthTrait;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $subscription = 'default', $plan = null)
    {
        if (env('SHOPIFY_BILLING_ENABLED', false) && (!env('SHOPIFY_FREMIUM', false) || ($plan !== null && is_numeric($plan)))) {
            if (!session()->has('shopify_domain')) {
                abort(403, 'Unauthorized action.');
            }
            
            $domain = session()->get('shopify_domain');
            $store = ShopifyShop::where('domain', $domain)
                ->firstOrFail();

            if ($this->subscribed($store, $subscription, $plan, func_num_args() === 2)) {
                if ($plan !== null) {
                    return redirect()->route('shopify.home');
                } else {
                    return $next($request);
                }
            }

            if($request->ajax() || $request->wantsJson()) {
                response('Subscription Required.', 402);
            }

            $shopify = Shopify::retrieve($store->domain, $store->token);

            if ($plan !== null) {
                $the_plan = ShopifyPlan::findOrFail($plan);

                $plan_name = $the_plan->name;
                $plan_price = $the_plan->price;
                $trial_days = $the_plan->trial_days;
            } else {
                $the_plan = ShopifyPlan::findOrFail(env('SHOPIFY_DEFAULT_PLAN_ID', 0));

                $plan_name = $the_plan->name;
                $plan_price = $the_plan->price;
                $trial_days = $the_plan->trial_days;
            }

            $options = [
                'name' => $plan_name,
                'price' => $plan_price,
                'trial_days' => $trial_days,
                'return_url' => route('shopify.billing'),
            ];

            if ($options['trial_days'] > 0) {
                $subscriptions = $store->subscriptions()
                    ->withTrashed()
                    ->take(1)
                    ->first();
                if ($subscriptions) { 
                    $now = Carbon::now();
                    $subDate = 0;
                    if ($now < $subscriptions->trial_ends_at) {
                        $subDate = $subscriptions->trial_ends_at->diffInDays($now);
                    }
                    $options['trial_days'] = $subDate;
                }
            }

            if(\App::environment('local') || env('SHOPIFY_BILLING_TEST', false)) {
                $options['test'] = true;
            }

            $redirectURL = ShopifyBilling::driver('RecurringBilling')
                ->create($shopify, $options)
                ->getRedirectURL();
            if ($request->has('hmac') && !$request->has('code')) {
                /* We are in the iframe - we need to redirect differently because of the iframe restrictions */
                return redirect()->route('shopify.billing_redirect', ['redirectUrl' => $redirectURL]);
            } else {
                return redirect($redirectURL);
            }
        } else {
            return $next($request);
        }
    }

    /**
     * Determine if the given user is subscribed to the given plan.
     *
     * @param  \App\Store  $store
     * @param  string  $subscription
     * @param  string  $plan
     * @param  bool  $defaultSubscription
     * @return bool
     */
    protected function subscribed($store, $subscription, $plan, $defaultSubscription)
    {
        if (! $store) {
            return false;
        }

        return ($defaultSubscription && $store->onGenericTrial()) || $store->subscribed($subscription, $plan);
    }
}