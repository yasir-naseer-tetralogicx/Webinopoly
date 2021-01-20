<?php

namespace App\Http\Controllers;

use App\AdditionalTab;
use App\Category;
use App\DefaultInfo;
use App\Image;
use App\Mail\NewUser;
use App\Mail\NewWallet;
use App\Product;
use App\ProductVariant;
use App\Questionaire;
use App\RetailerImage;
use App\RetailerProduct;
use App\RetailerProductVariant;
use App\ShippingRate;
use App\SubCategory;
use App\User;
use App\WarnedPlatform;
use App\Zone;
use Automattic\WooCommerce\Client;
use Automattic\WooCommerce\HttpClient\HttpClientException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use OhMyBrew\ShopifyApp\Models\Shop;
use OhMyBrew\ShopifyApp\ShopifyApp;
use Spatie\Permission\Models\Role;

class HelperController extends Controller
{
    public function getShop(){
        $current_shop = \OhMyBrew\ShopifyApp\Facades\ShopifyApp::shop();
        return Shop::where('shopify_domain',$current_shop->shopify_domain)->first();
    }
    public function getAdminShop(){
        return Shop::where('shopify_domain','wefullfill.myshopify.com')->first();
    }

    public function getWooCommerceAdminShop() {
        return new Client(env('WOOCOMMERCE_ADMIN_SHOP'), env('WOOCOMMERCE_CONSUMMER_KEY'), env('WOOCOMMERCE_CONSUMMER_SECRET'), ['wp_api' => true, 'version' => 'wc/v3',]);
    }
    public function getLocalShop(){
        /*Ossiset Shop Model*/
        $shop =  \OhMyBrew\ShopifyApp\Facades\ShopifyApp::shop();
        /*Local Shop Model!*/
        $shop= \App\Shop::find($shop->id);
        return $shop;
    }
    public  function getSpecificShop($id){
        return Shop::find($id);
    }

    public  function getSpecificLocalShop($id){
        $shop= \App\Shop::find($id);
        return $shop;
    }

    public function reset_all(Request $request){
        if($request->has('pass')){
            if($request->input('pass')== 'fantasy-reset')
            {
                AdditionalTab::truncate();
                Category::truncate();
                DB::table('category_product')->truncate();
                DB::table('cities')->truncate();
                DB::table('countries')->truncate();
                DB::table('country_zone')->truncate();
                DefaultInfo::truncate();
                Image::truncate();
                Product::truncate();
                DB::table('product_platform')->truncate();
                ProductVariant::truncate();
                ShippingRate::truncate();
                DB::table('states')->truncate();
                DB::table('subcategory_product')->truncate();
                SubCategory::truncate();
                User::truncate();
                WarnedPlatform::truncate();
                Zone::truncate();
                DB::table('user_shop')->truncate();
                DB::table('model_has_roles')->truncate();

                Artisan::call('db:seed');
                $this->SuperAdminCreate();

                return "Database Reset successfully executed";
            }
        }
        else{
            return 'Please Enter Password for Reset';
        }
    }

    public function SuperAdminCreate()
    {
        if (!User::where('email', 'super_admin@wefullfill.com')->exists()) {
            User::create([
                'name' => 'Super Admin',
                'email' => 'super_admin@wefullfill.com',
                'password' => Hash::make('wefullfill@admin'),
            ]);
        }
        if (!User::where('email', 'super_admin@wefullfill.com')->exists()) {
            User::create([
                'name' => 'Super Admin',
                'email' => 'admin@wefullfill.com',
                'password' => Hash::make('wefullfill@admin'),
            ]);
        }
    }

    public function deleteRetailer(){
        RetailerProduct::truncate();
        RetailerProductVariant::truncate();
        RetailerImage::truncate();
        DB::table('retailer_product_category')->truncate();
        DB::table('retailer_product_subcategory')->truncate();
        DB::table('retailer_product_shop')->truncate();
        DB::table('retailer_product_user')->truncate();

    }

    public function QuestionnaireCheck(Request $request){
        if($request->has('shop')){
            $shop = \App\Shop::find($request->input('shop'));
            if($shop != null){

                if(count($shop->has_user) > 0){
                    $array = $shop->has_user->pluck('id')->toArray();
                    $filled_questionnaire = Questionaire::whereIn('user_id',$array)->first();
                    if($filled_questionnaire == null){
                        $questionnaire = Questionaire::where('shop_id',$shop->id)->first();
                        if($questionnaire == null){
                            return response()->json([
                                'popup' => 'yes'
                            ]);
                        }
                        else{
                            return response()->json([
                                'popup' => 'no'
                            ]);
                        }

                    }
                    else{
                        return response()->json([
                            'popup' => 'no'
                        ]);
                    }
                }
                else{
                    $filled_questionnaire = Questionaire::where('shop_id',$shop->id)->first();
                    if($filled_questionnaire == null){
                        return response()->json([
                            'popup' => 'yes'
                        ]);
                    }
                    else{
                        return response()->json([
                            'popup' => 'no'
                        ]);
                    }
                }
            }
            else{
                return response()->json([
                    'popup' => 'no'
                ]);
            }
        }
        elseif($request->has('user')){
            $user = User::find($request->input('user'));
            if($user != null){
                if(count($user->has_shops) > 0){
                    $array = $user->has_shops->pluck('id')->toArray();
                    $filled_questionnaire = Questionaire::whereIn('shop_id',$array)->first();
                    if($filled_questionnaire == null){
                        return response()->json([
                            'popup' => 'yes'
                        ]);
                    }
                    else{
                        return response()->json([
                            'popup' => 'no'
                        ]);
                    }
                }
                else{
                    $filled_questionnaire = Questionaire::where('user_id',$user->id)->first();
                    if($filled_questionnaire == null){
                        return response()->json([
                            'popup' => 'yes'
                        ]);
                    }
                    else{
                        return response()->json([
                            'popup' => 'no'
                        ]);
                    }
                }
            }
            else{
                return response()->json([
                    'popup' => 'no'
                ]);
            }
        }
    }

    public function SaveQuestionnaire(Request $request){
        $q = new Questionaire();
        $q->gender =  $request->input('gender');
        $q->dob =  $request->input('dob');
        $q->new_to_business =  $request->input('new_to_business');
        $q->countries =  implode(',',$request->input('countries'));
        $q->product_ranges =  implode(',',$request->input('product_ranges'));
        $q->delivery_time =  $request->input('delivery_time');
        $q->concerns =  implode(',',$request->input('concerns'));
        if($request->has('shop_id')){
            $q->shop_id =  $request->input('shop_id');
        }
        if($request->has('user_id')){
            $q->user_id =  $request->input('user_id');
        }
        $q->save();
        return redirect()->back()->with('success','Questionnaire Submitted ! Thanks for your time !');
    }

    public function testEmail(){
        $user = User::all()->first();
        Mail::to('fazalkhann66@gmail.com')->send(new NewWallet($user));
    }


    public function test(){
        $shop = $this->getSpecificShop('4');
//        $res = $shop->api()->rest('GET','/admin/orders/2647280091233.json');
//        dd($res);

        $response = $shop->api()->rest('GET', '/admin/orders/count.json',['status' => 'any']);
        if(!$response->errors) {
            $count = $response->body->count;
            $iterations = ceil($count / 50);
            $next = '';

            for ($i = 1; $i <= $iterations; $i++) {
                if ($i == 1) {
                    $product_response = $shop->api()->rest('GET', '/admin/orders.json',['status'=>'any']);
                } else {
                    $product_response = $shop->api()->rest('GET', '/admin/orders.json', ['page_info' => $next],['status'=>'any']);
                }

                dd($product_response);
                if(!$product_response->errors) {

                    if($product_response->link != null){
                        $next = $product_response->link->next;
                    }

                    $orders = $product_response->body->orders;
                    foreach ($orders as $order){
                     $response = $shop->api()->rest('DELETE', '/admin/orders/'.$order->id.'.json');
                    dd($response);
                    }
                }

            }
        }
    }

}
