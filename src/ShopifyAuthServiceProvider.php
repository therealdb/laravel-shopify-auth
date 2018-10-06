<?php

namespace TheRealDb\ShopifyAuth;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use BNMetrics\Shopify\Contracts\BillingFactory;

class ShopifyAuthServiceProvider extends ServiceProvider
{
    public function boot(\Illuminate\Routing\Router $router)
    {
        /* Define our routes */
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');

        /* Define our migrations */
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');

        /* Define our default views */
        $this->loadViewsFrom(__DIR__.'/resources/views', 'shopify-auth');

        /* Publish our public assets */
		$this->publishes([
			__DIR__.'/public' => public_path('vendor/shopify-auth'),
		], 'public');

		$this->publishes([
			__DIR__.'/config/shopifyauth.php' => config_path('shopifyauth.php'),
		], 'config');

		/* BNMetrics config needs to be registered - can be overwritten if user publishes the vendor package */
		$reflection = new \ReflectionClass(\BNMetrics\Shopify\Shopify::class);
		$theConfigPath = dirname($reflection->getFileName());
		$theConfigFile = $theConfigPath . "/config/shopify.php";
		$this->mergeConfigFrom(
	        $theConfigFile, 'shopify'
	    );

		/* Register the custom Middleware */
		$router->aliasMiddleware('shopifyshopauth', 'TheRealDb\ShopifyAuth\Http\Middleware\ShopifyShopAuth');
		$router->aliasMiddleware('shopifyshopbilling', 'TheRealDb\ShopifyAuth\Http\Middleware\ShopifyShopCharge');
		$router->aliasMiddleware('shopifyshopwebhook', 'TheRealDb\ShopifyAuth\Http\Middleware\ShopifyWebhook');

        \Socialite::extend('shopify', function ($app) {
            $config = $app['config']['services.shopify'];

            return \Socialite::buildProvider('\SocialiteProviders\Shopify\Provider', $config);
        });
        
		/* Register our event listener for Socialite */
		Event::listen('\SocialiteProviders\Manager\SocialiteWasCalled::class', 'SocialiteProviders\\Shopify\\ShopifyExtendSocialite@handle');

		$this->app->bind(BNMetrics\Shopify\Contracts\BillingFactory::class, function ($app) {
		      return new BNMetrics\Shopify\BillingServiceProvider($app);
		});
    }

    public function register()
    {
    	/* Register our api connection settings in the services config */
    	config(['services.shopify' => [
        	'client_id' => env('SHOPIFY_KEY'),
    		'client_secret' => env('SHOPIFY_SECRET'),
    		'redirect' => env('SHOPIFY_REDIRECT_URI')
        ]]);

    	/* Register the BNMetrics Shopify API Facades */
		$loader = \Illuminate\Foundation\AliasLoader::getInstance();
		$loader->alias('Shopify', \BNMetrics\Shopify\Facade\ShopifyFacade::class);
		$loader->alias('ShopifyBilling', \BNMetrics\Shopify\Facade\BillingFacade::class);

        $this->app->singleton(
            \BNMetrics\Shopify\Contracts\BillingFactory::class, function($app) {
                return new \BNMetrics\Shopify\Billing\ShopifyBillingManager($app);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
        	BNMetrics\Shopify\ShopifyServiceProvider::class,
        	BNMetrics\Shopify\BillingServiceProvider::class
        ];
    }
}