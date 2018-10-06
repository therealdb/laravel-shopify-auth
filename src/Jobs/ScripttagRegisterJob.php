<?php

namespace TheRealDb\ShopifyAuth\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use TheRealDb\ShopifyAuth\Http\Models\ShopifyShop;
use Shopify;

class ScripttagRegisterJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var \TheRealDb\ShopifyAuth\Http\Models\ShopifyShop $shop The active shop model
     */
    protected $shop;

    /**
     * @var array $tags The script tags to install
     */
    protected $tags;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(ShopifyShop $shop, $tags)
    {
        $this->shop = $shop;
        $this->tags = $tags;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $myShop = \Shopify::retrieve($this->shop->domain, $this->shop->token);
        $scripts = $myShop->getScriptTagsAll();

        /* Check scripts to register */
        if (isset($this->tags['register'])) {
            foreach ($this->tags['register'] as $tag) {
                if (!$this->isAlreadyInstalled($tag['src'], $scripts['script_tags'])) {
                    $myShop->createScriptTags(["script_tag" => $tag]);
                }
            }
        }

        /* Check scripts to unregister */
        if (isset($this->tags['unregister'])) {
            foreach ($this->tags['unregister'] as $tag) {
                if ($this->isAlreadyInstalled($tag['src'], $scripts['script_tags'])) {
                    $theId = $this->getScriptTagId($tag['src'], $scripts['script_tags']);
                    if ($theId !== false) {
                        $myShop->deleteScriptTags($this->getScriptTagId($tag['src'], $scripts['script_tags']));
                    }
                }
            }
        }
    }

    /**
     * Check if the scripttag working with
     *
     * @param $src The tag we're trying to install
     * @param $scripts Array of already installed script tags
     *
     * @return boolean
     */
    protected function isAlreadyInstalled($src, $scripts)
    {
        if (count($scripts) > 0) {
            foreach ($scripts as $script) {
                if ($script['src'] == $src) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Get the ID of the script tag
     *
     * @param $src The tag we're working with
     * @param $scripts Array of already installed script tags
     *
     * @return boolean
     */
    protected function getScriptTagId($src, $scripts)
    {
        if (count($scripts) > 0) {
            foreach ($scripts as $script) {
                if ($script['src'] == $src) {
                    return $script['id'];
                }
            }
        }

        return false;
    }
}
