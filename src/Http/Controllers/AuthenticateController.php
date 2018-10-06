<?php

namespace TheRealDb\ShopifyAuth\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Exception;
use Socialite;
use Shopify;

/* Models */
use TheRealDb\ShopifyAuth\Http\Models\ShopifyShop;

/* Custom Requests */
use TheRealDb\ShopifyAuth\Http\Requests\ShopifyAuthLoginRequest;

/* Traits */
use TheRealDb\ShopifyAuth\Http\Traits\ShopifyAuthTrait;

/* Jobs */
use TheRealDb\ShopifyAuth\Jobs\ScripttagRegisterJob;

class AuthenticateController extends Controller
{
	use ShopifyAuthTrait;

	/**
     * redirect when user submits login form with domain
     * 
     * @param TheRealDb\ShopifyAuth\Http\Requests\ShopifyAuthLoginRequest $request
     */
	public function attemptLogin(ShopifyAuthLoginRequest $request)
	{
		return redirect()->route('shopify.login', ['shop' => $request->shop]);
	}

    /**
     * Method to catch the authenticate route
     * 
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     */
    public function authenticate(Request $request)
    {
    	if (!$request->has('shop') && !$request->has('code')) {
    		/* We cannot authenticate without a shop */
    		return redirect()->route('shopify.login');
    	}

    	if ($request->has('code')) {
    		return $this->authenticateWithCode($request);
    	} else {

	    	$domain = $request->has('shop');

	    	/* Check if the domain is valid - if not, throw exception */
	    	if (!$this->isValidDomain($domain)) {
	    		throw new Exception('Domain is not valid.');
	    	}

		   	/* Make sure the session expires on close if the config has not already been set */
		  	config(['session.expire_on_close' => true]);

		  	$isEmbedded = env('SHOPIFY_EMBEDDED', false);
		  	if ($isEmbedded) {
			  	$url = Socialite::with('shopify')
			  		->scopes(config('shopifyauth.scopes'))
			  		->redirect()
			  		->getTargetUrl();

		  		return view('shopify-auth::authenticate')
		  			->with('url', $url);
			} else {
		  		return Socialite::with('shopify')
			  		->scopes(config('shopifyauth.scopes'))
			  		->redirect();
			}
	  	}
    }

    /**
     * Authenticate the user when the code parameter exists in the URL
     * 
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     */
    public function authenticateWithCode(Request $request)
    {
    	$user = Socialite::with('shopify')->user();

    	$shop = ShopifyShop::withTrashed()
    		->firstOrCreate(['domain' => $user->user['domain']]);

    	if ($shop->trashed()) {
    		$shop->restore();
    	}

    	$shop->token = $user->token;
    	$shop->save();

    	$config = config('shopifyauth.script_tags');
    	if (env('SHOPIFY_INSTALL_SCRIPTTAGS', true) && $config && ((isset($config['register']) && is_array($config['register']) && count($config['register']) > 0) || (isset($config['unregister']) && is_array($config['unregister']) && count($config['unregister']) > 0))) {
	    	ScripttagRegisterJob::dispatch($shop, $config);
	    }

        // Run any initialize jobs we want
        $this->initializeJob($shop);

    	return redirect()->route('shopify.home');
    }

    /**
     * Run any subsequent jobs once the user has authenticated
     *
     * @return boolean
     */
    public function initializeJob($shop)
    {
        $config = config('shopifyauth.initialize_jobs');
        
        if (!is_array($config)) {
            // Our array isn't set up properly
            return false;
        }

        $run = function($config) use ($shop) {
            $job = new $config['job']($shop);
            if (isset($config['dispatch']) && $config['dispatch'] === true) {
                dispatch($job);
            } else {
                $job->handle();
            }

            return true;
        };

        if (count($config) > 0) {
            foreach ($config as $job) {
                $run($job);
            }

            return true;
        }

        // We didn't run into any jobs
        return false;
    }
}
