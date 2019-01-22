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
            $this->shop->token = null;
            $this->shop->save();

            /* Cancel any subscriptions */
            if ($this->force) {
                $this->shop->subscriptions()->forceDelete();
            } else {
                $this->shop->subscriptions()->delete();
            }

            /* Soft Delete the shop */
            if ($this->force) {
                $this->shop->forceDelete();
            } else {
                $this->shop->delete();
            }

            $config = config('shopifyauth.uninstall_jobs');
        
            if (is_array($config)) {
                $shop = $this->shop;
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
                }
            }

            return true;
        }

        return false;
    }
}
