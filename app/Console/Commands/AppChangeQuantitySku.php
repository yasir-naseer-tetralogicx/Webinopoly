<?php

namespace App\Console\Commands;

use App\Http\Controllers\HelperController;
use App\Product;
use Illuminate\Console\Command;

class AppChangeQuantitySku extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sku-quantity-change {product_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'App Sku and Quantity Exchange';

    /**
     * Create a new command instance.
     *
     * @return void
     */

    private $helper;
    public function __construct()
    {
        $this->helper = new HelperController();
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $product_id = $this->argument('product_id');
        if($product_id != null){
            $product = Product::find($product_id);
            if($product != null){
                foreach ($product->has_retailer_products as $retailer_product){
                    if(count($retailer_product->hasVariants) > 0){
                        /*Quantity and Sku Save*/
                        foreach ($retailer_product->hasVariants as $index => $v){
                            $v->sku = $product->hasVariants[$index]->sku;
                            $v->quantity =  $product->hasVariants[$index]->quantity;
                            $v->save();

                            $productdata = [
                                "variant" => [
                                    'sku' => $v->sku,
                                ]
                            ];
                            $shop = $this->helper->getSpecificShop($v->shop_id);
                            if($shop != null){
                                $resp =  $shop->api()->rest('PUT', '/admin/api/2019-10/products/'.$retailer_product->shopify_id.'/variants/'.$v->shopify_id.'.json',$productdata);
                                sleep(1);
                            }

                        }
                    }
                    else{
                        $retailer_product->sku = $product->sku;
                        $retailer_product->quantity = $product->quantity;
                        $retailer_product->save();
                        $shop = $this->helper->getSpecificShop($retailer_product->shop_id);
                        if($shop != null){
                            $response = $shop->api()->rest('GET', '/admin/api/2019-10/products/' . $retailer_product->shopify_id .'.json');
                            if(!$response->errors){
                                $shopifyVariants = $response->body->product->variants;
                                $variant_id = $shopifyVariants[0]->id;
                                $i = [
                                    'variant' => [
                                        'sku' => $retailer_product->sku,

                                    ]
                                ];
                                $shop->api()->rest('PUT', '/admin/api/2019-10/variants/' . $variant_id .'.json', $i);
                                sleep(1);
                            }
                        }
                    }

                }
            }
        }

    }
}
