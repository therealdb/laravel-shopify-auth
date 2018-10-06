<?php

namespace TheRealDb\ShopifyAuth\Http\Middleware;

use Closure;
use TheRealDb\ShopifyAuth\Http\Models\ShopifyShop;

/* Traits */
use TheRealDb\ShopifyAuth\Http\Traits\ShopifyAuthTrait;

class ShopifyWebhook
{
    use ShopifyAuthTrait;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $hmac = request()->header('x-shopify-hmac-sha256') ?: '';
        $shop = request()->header('x-shopify-shop-domain');
        $data = request()->getContent();

        $hmacLocal = base64_encode(hash_hmac('sha256', $data, env('SHOPIFY_SECRET', ''), true));
        if (!hash_equals($hmac, $hmacLocal) || empty($shop)) {
            abort(401, 'Invalid webhook signature');
        }

        return $next($request);
    }
}