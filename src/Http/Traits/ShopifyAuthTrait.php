<?php

namespace TheRealDb\ShopifyAuth\Http\Traits;

trait ShopifyAuthTrait {

	/**
	 * @var $shop  The current shop
	 */
	public $shop;

	public function getShop()
	{
		$domain = request()->session()->get('shopify_domain');
		if (!$domain) {

		}

		return $this->shop;
	}

	public function isValidDomain($domain)
	{
		return true;
	}
}