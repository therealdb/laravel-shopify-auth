<?php

namespace TheRealDb\ShopifyAuth\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use TheRealDb\ShopifyAuth\Http\Models\ShopifyShop;

class UninstallJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var string $domain The shop domain
     */
    protected $domain;

    /**
     * @var mixed $data The webhook data
     */
    protected $data;

    /**
     * @var \TheRealDb\ShopifyAuth\Http\Models\ShopifyShop $shop The active shop model
     */
    protected $shop;

    /**
     * @var boolean $force Should force delete store
     */
    protected $force;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($domain, $data, $force)
    {
        $this->domain = $domain;
        $this->force = $force;
        $this->data = $data;
        $this->shop = ShopifyShop::withTrashed()
            ->where('domain', $domain)
            ->firstOrFail();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->shop) {

            /* Remove token from shop */
            $this->shop->shopify_token = null;
            $this->shop->save();

            /* Cancel any subscriptions */
            if ($this->force) {
                $this->shop->subscriptions()->forceDelete();
            } else {
                $this->shop->subscriptions()->delete();
            }

            /* Soft Delete the shop */
            if ($this->force) {
                $this->shop()->forceDelete();
            } else {
                $this->shop()->delete();
            }

            return true;
        }

        return false;
    }
}
