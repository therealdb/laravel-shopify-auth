<?php

return [

	/*
    |--------------------------------------------------------------------------
    | Views
    |--------------------------------------------------------------------------
    |
    | Use this view array to override the built-in views
    |
    */
	'views' => [
		//'dashboard' => 'your.blade.view', // The main '/' route of the app
		//'login' => 'your.blade.view', // the login page
		//'declined' => 'your.blade.view', // The charge declined page
	],

	/*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    |
    | Define the scopes your app requires
    |
    */
	'scopes' => [
		//'read_content', 'write_content', // Content
		//'read_themes', 'write_themes', // Themes
		'read_products', 'write_products', 'read_product_listings' // Products
		//'read_customers', 'write_customers',
		//'read_orders', 'write_orders', 'read_all_orders', 'read_draft_orders', 'write_draft_orders', // Orders
		//'read_inventory', 'write_inventory', // Inventory
		//'read_locations', // Locations
		//'read_fulfillments', 'write_fulfillments' // Fulfilment
		//'read_shipping', 'write_shipping', // Shipping
		//'read_script_tags', 'write_script_tags' // Script Tags
	],

	/*
    |--------------------------------------------------------------------------
    | Script Tags
    |--------------------------------------------------------------------------
    |
    | Define the script tags your app will need. A check is run on every app
    | to register and unregister tags defined here.
    |
    */
	'script_tags' => [
		/* Use this array to define new scripts you want to register */
		'register' => [
			/*
			[
				'src' => 'https://example.com/myscript.js',
				'event' => 'onload',
				'display_scope' => 'online_store' // Available values: online_store, order_status, all
			]
			*/
		],

		/* Use this array to define existing script tags you may want to remove - this gets run whenever a user opens the app */
		'unregister' => [
			/*
			[
				'src' => 'https://example.com/myscript.js'
			]
			*/
		]
	],

	/*
    |--------------------------------------------------------------------------
    | Initialization Jobs
    |--------------------------------------------------------------------------
    |
    | These are jobs that need to be run once the user is authorized.
    |
    */
	'initialize_jobs' => [
		/*
		[
			'job' => '', // App\Jobs\MyJob::class
			'dispatch' => false // false=run immediately | true=run as normal job
		]
		*/
	],

	/*
    |--------------------------------------------------------------------------
    | Webhooks
    |--------------------------------------------------------------------------
    |
    | This will install webhooks for your app. The uninstall webhook is 
    | is pre-configured to work at /webhook/uninstall out of the box.
    |
    */
	'webhooks' => [
		/*
		[
			'topic' => 'app/uninstalled',
			'address' => 'https://example.com/webhook/uninstall'
		]
		*/
	]
];