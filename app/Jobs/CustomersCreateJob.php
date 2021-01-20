<?php namespace App\Jobs;

use App\Customer;
use App\Shop;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CustomersCreateJob implements ShouldQueue
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
        $customer =  $this->data;
        $shop = Shop::where('shopify_domain', $this->shopDomain)->first();
        if (Customer::where('customer_shopify_id',$customer->id)->exists()){
            $new_customer = Customer::where('customer_shopify_id',$customer->id)->first();
        }
        else{
            $new_customer = new Customer();
        }
        $new_customer->customer_shopify_id = $customer->id;
        $new_customer->first_name = $customer->first_name;
        $new_customer->last_name = $customer->last_name;
        $new_customer->phone = $customer->phone;
        $new_customer->email = $customer->email;
        $new_customer->total_spent = $customer->total_spent;
        $new_customer->shop_id = $shop->id;
        if(count($shop->has_user) > 0){
            $new_customer->user_id = $shop->has_user[0]->id;
        }
        $new_customer->save();
    }
}
