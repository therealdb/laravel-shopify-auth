<?php

namespace TheRealDb\ShopifyAuth\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use TheRealDb\ShopifyAuth\Http\Models\ShopifyShop;
use Slince\Shopify\PrivateAppCredential;
use Slince\Shopify\Client;

class WebhookRegisterJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var \TheRealDb\ShopifyAuth\Http\Models\ShopifyShop $shop The active shop model
     */
    protected $shop;

    /**
     * @var array $hooks The webhooks to install
     */
    protected $hooks;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(ShopifyShop $shop, $hooks)
    {
        $this->shop = $shop;
        $this->hooks = $hooks;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $credential = new PublicAppCredential($this->shop->token);
        $client  = new Client($credential, $this->shop->domain, [
            'metaCacheDir' => './tmp' // Metadata cache dir, required
        ]);

        $webhooks = $client->getWebhookManager()->findAll();

        /* Check scripts to register */
        if (isset($this->hooks)) {
            foreach ($this->hooks as $hook) {
                if (isset($hook['topic']) && isset($hook['address']) && !$this->isAlreadyInstalled($hook['address'], $webhooks)) {
                    $client->getWebhookManager()->create([
                        'topic' => $hook['topic'],
                        'address' => $hook['address']
                    ]);
                }
            }
        }
    }

    /**
     * Check if the webhook is already installed
     *
     * @param $address The webhook address we're trying to install
     * @param $webhooks Array of already installed webhooks
     *
     * @return boolean
     */
    protected function isAlreadyInstalled($address, $webhooks)
    {
        if (count($webhooks) > 0) {
            foreach ($webhooks as $webhook) {
                if ($webhook->address == $address) {
                    return true;
                }
            }
        }

        return false;
    }
}
