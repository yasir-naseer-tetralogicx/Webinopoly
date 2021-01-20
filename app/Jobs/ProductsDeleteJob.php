<?php

namespace App\Jobs;

use App\RetailerProduct;
use App\RetailerProductVariant;
use App\Shop;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProductsDeleteJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Shop's myshopify domain
     *
     * @var string
     */
    public $shopDomain;

    /**
     * The webhook data
     *
     * @var object
     */
    public $data;


    /**
     * Create a new job instance.
     *
     * @param string $shopDomain The shop's myshopify domain
     * @param object $data    The webhook data (JSON decoded)
     *
     * @return void
     */
    public function __construct($shopDomain, $data)
    {
        $this->shopDomain = $shopDomain;
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $response = $this->data;
        $shop = Shop::where('shopify_domain', $this->shopDomain)->first();
        $product = RetailerProduct::where('shopify_id', $response->id)->first();

        if($product != null) {
            foreach ($product->hasVariants as $variant) {
                $variant->delete();
            }
            foreach ($product->has_images as $image){
                $image->delete();
            }
            $product->has_categories()->detach();
            $product->has_subcategories()->detach();

            $shop->has_imported()->detach([$product->linked_product_id]);
            if(count($shop->has_user) > 0){
                $shop->has_user[0]->has_imported()->detach([$product->linked_product_id]);
            }
            $product->delete();
        }

    }
}
