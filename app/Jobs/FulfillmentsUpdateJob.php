<?php namespace App\Jobs;

use App\ErrorLog;
use App\Http\Controllers\AdminWebhookController;
use App\OrderFulfillment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FulfillmentsUpdateJob implements ShouldQueue
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
        $data = $this->data;
        if ($this->shopDomain == 'wefullfill.myshopify.com') {
            $webhook = new AdminWebhookController();
            $fulfillment = OrderFulfillment::where('admin_fulfillment_shopify_id',$data->id)->first();
            if($fulfillment != null){
                if($data->status == 'cancelled'){
                    $webhook->unset_fulfillments($data);
                }
                if($data->status == 'success'){
                    $webhook->set_tracking_details($data);
                }
            }
        }
    }
}
