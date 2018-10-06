<?php

Route::group(['middleware' => ['web']], function () {
	
	/*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    |
    | Displays the dashboard
    |
    */
	Route::get('/', function() {

		$domain = session()->get('shopify_domain');
		$shop = \TheRealDb\ShopifyAuth\Http\Models\ShopifyShop::where('domain', $domain)
	    	->firstOrFail();

		$config = config('shopifyauth.script_tags');
    	if (env('SHOPIFY_INSTALL_SCRIPTTAGS', true) && $config && ((isset($config['register']) && is_array($config['register']) && count($config['register']) > 0) || (isset($config['unregister']) && is_array($config['unregister']) && count($config['unregister']) > 0))) {
	    	\TheRealDb\ShopifyAuth\Jobs\ScripttagRegisterJob::dispatch($shop, $config);
	    }

	    $configDashboard = config('shopifyauth.views');
	    if ($configDashboard && isset($configDashboard['dashboard'])) {
	    	return view($configDashboard['dashboard'])
	    		->with('shop', $shop);
	    } else {
			return view("shopify-auth::dashboard");
		}
	})
		->middleware(['shopifyshopauth', 'shopifyshopbilling'])
		->name('shopify.home');

	/*
    |--------------------------------------------------------------------------
    | Login
    |--------------------------------------------------------------------------
    |
    | Displays the login page
    |
    */
	Route::get('/login', function() {
		$configDashboard = config('shopifyauth.views');
	    if ($configDashboard && isset($configDashboard['login'])) {
	    	return view($configDashboard['login']);
	    } else {
			return view("shopify-auth::login");
		}
	})
		->middleware('shopifyshopauth')
		->name('shopify.login');

	/*
    |--------------------------------------------------------------------------
    | Login POST
    |--------------------------------------------------------------------------
    |
    | Handles the login form post
    |
    */
	Route::post(
		'/login',
		'TheRealDb\ShopifyAuth\Http\Controllers\AuthenticateController@attemptLogin'
	);

	/* Declined billing route */
	Route::get('/declined', function() {
		$configDashboard = config('shopifyauth.views');
	    if ($configDashboard && isset($configDashboard['declined'])) {
	    	return view($configDashboard['declined']);
	    } else {
			return view("shopify-auth::declined");
		}
	})
		->name('shopify.declined');

	/*
    |--------------------------------------------------------------------------
    | Authentication
    |--------------------------------------------------------------------------
    |
    | Handles authenticating a store with Shopify
    |
    */
	Route::get(
		'/authenticate',
		'TheRealDb\ShopifyAuth\Http\Controllers\AuthenticateController@authenticate'
	)
		->name('shopify.authenticate');

	/*
    |--------------------------------------------------------------------------
    | Billing
    |--------------------------------------------------------------------------
    |
    | Handles creating the charge request for a shop
    |
    */
	Route::get('/billing', function(\Illuminate\Http\Request $request) {
	    $domain = session()->get('shopify_domain');
	    $store = \TheRealDb\ShopifyAuth\Http\Models\ShopifyShop::where('domain', $domain)
	    	->firstOrFail();

	    $shopify = \Shopify::retrieve($store->domain, $store->token);

	    try {
	    	$activated = \ShopifyBilling::driver('RecurringBilling')
	        	->activate($store->domain, $store->token, $request->get('charge_id'));
	    } catch (\Exception $e) {
	    	return redirect()->route('shopify.declined');
	    }
	    $response = array_get($activated->getActivated(), 'recurring_application_charge');

	    \TheRealDb\ShopifyAuth\Http\Models\ShopifyCharge::create([
	        'shopify_shop_id' => $store->id,
	        'name' => 'default',
	        'charge_id' => $request->get('charge_id'),
	        'plan' => array_get($response, 'name'),
	        'quantity' => 1,
	        'charge_type' => \TheRealDb\ShopifyAuth\Http\Models\ShopifyCharge::CHARGE_RECURRING,
	        'test' => array_get($response, 'test'),
	        'trial_ends_at' => array_get($response, 'trial_ends_on'),
	    ]);

	    return redirect()
	    	->route('shopify.home');
	})
	->middleware(['shopifyshopauth'])
	->name('shopify.billing');

	/*
    |--------------------------------------------------------------------------
    | Billing redirect
    |--------------------------------------------------------------------------
    |
    | Handles the billing redirect
    |
    */
	Route::get('/billing_redirect', function(\Illuminate\Http\Request $request) {
		return view('shopify-auth::billing_redirect')
            ->with('url', $request->input('redirectUrl'));
    })
    ->name('shopify.billing_redirect');;

    /*
    |--------------------------------------------------------------------------
    | Uninstall Webhook Handler
    |--------------------------------------------------------------------------
    |
    | Handles incoming uninstall webhook.
    |
    */
    Route::post(
        '/webhook/uninstall',
        'TheRealDb\ShopifyAuth\Http\Controllers\WebhookController@uninstall'
    )
    ->middleware('shopifyshopwebhook')
    ->name('webhook.uninstall');

    /*
    |--------------------------------------------------------------------------
    | Redact Webhook Handler
    |--------------------------------------------------------------------------
    |
    | Handles incoming redact webhook.
    |
    */
    Route::post(
        '/webhook/uninstall/redact',
        'TheRealDb\ShopifyAuth\Http\Controllers\WebhookController@redact'
    )
    ->middleware('shopifyshopwebhook')
    ->name('webhook.redact');
});