<?php

namespace TheRealDb\ShopifyAuth\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

/* Jobs */
use TheRealDb\ShopifyAuth\Jobs\UninstallJob;

class WebhookController extends Controller
{
	/**
	 * Uninstall Store
	 *
	 * @param boolean $force Whether to force remove all store content
	 */
	public function uninstall($force=false)
	{
		$shopDomain = request()->header('x-shopify-shop-domain');
        $data = json_decode(request()->getContent());

        UninstallJob::dispatch($shopDomain, $data, $force);

        return response()
        	->json([
        		'status' => 'success'
        	], 200);
	}

	/**
	 * Redact store data (force delete)
	 */
	public function redact()
	{
		return $this->uninstall(true);
	}
}