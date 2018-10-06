<?php

namespace TheRealDb\ShopifyAuth\Http\Middleware;

use Closure;
use TheRealDb\ShopifyAuth\Http\Models\ShopifyShop;

/* Traits */
use TheRealDb\ShopifyAuth\Http\Traits\ShopifyAuthTrait;

class ShopifyShopAuth
{
    use ShopifyAuthTrait;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->has('shop') && $request->input('shop') != "" && $this->isValidDomain($request->input('shop'))) {
            /* Store either doesn't exist or has been trashed - we should re-authorize */

            $theDomain = $request->input('shop');

            /* Forget our current shopify domain session */
            session()->put('shopify_domain', $request->input('shop'));

            if (!$request->has('code') && !$request->has('hmac')) {
                /* Send the user to authenticate */
                return redirect()->route('shopify.authenticate', ['shop' => $theDomain]);
            }
        } else if (!session()->has('shopify_domain') && $request->route()->getName() != "shopify.login") {
            return redirect()->route('shopify.login');
        } else {
            if (session()->has('shopify_domain') && !ShopifyShop::where('domain', session()->get('shopify_domain'))->first()) {
                session()->forget('shopify_domain');
                return redirect()->route('shopify.login');
            }
        }

         /* All is good - store exists and is not trashed */
        return $next($request);
    }
}