<?php

namespace App\Http\Controllers;

use App\Category;
use App\Image;
use App\Mail\OrderPlaceEmail;
use App\Mail\OrderStatusMail;
use App\Mail\WishlistApproveMail;
use App\Mail\WishlistComplateMail;
use App\Mail\WishlistRejectMail;
use App\Mail\WishlistReqeustMail;
use App\ManagerLog;
use App\Product;
use App\ProductVariant;
use App\RetailerImage;
use App\RetailerProduct;
use App\RetailerProductVariant;
use App\Tag;
use App\User;
use App\WarnedPlatform;
use App\Wishlist;
use App\WishlistAttachment;
use App\WishlistCountry;
use App\WishlistThread;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use OhMyBrew\ShopifyApp\Models\Shop;

class WishlistController extends Controller
{

    private $helper;
    private $notify;
    private $log;
    private $woocommerce_helper;

    /**
     * WishlistController constructor.
     * @param $helper
     */
    public function __construct()
    {
        $this->helper = new HelperController();
        $this->notify = new NotificationController();
        $this->log = new ActivityLogController();
        $this->woocommerce_helper = new ProductController();
    }

    public function create_wishlist(Request $request){
        $manager = User::find($request->input('manager_id'));
        if($manager != null){
            $wish = new Wishlist();
            $wish->product_name = $request->input('product_name');
            $wish->cost = $request->input('cost');
            $wish->monthly_sales = $request->input('monthly_sales');
            $wish->description = $request->input('description');
            $wish->reference = $request->input('reference');
            $wish->status_id = '1';
            $wish->manager_id = $manager->id;
            $user = null;
            if($request->type == 'shopify-user-wishlist'){
                $shop = $this->helper->getLocalShop();
                $user = $shop->has_user()->first();
                $wish->user_id = $user->id;
                $wish->shop_id = $request->input('shop_id');
            }
            else{
                $wish->user_id = Auth::id();
                $wish->shop_id = $request->input('shop_id');
            }

            $wish->save();
            $wish->has_market()->attach($request->input('countries'));

            /*Wishlist request email*/
            $user = User::find($wish->user_id);

            $manager_email = $manager->email;
            $users_temp =['info@wefullfill.com',$manager_email];
            $users = [];

            foreach($users_temp as $key => $ut){
                if($ut != null) {
                    $ua = [];

                    $ua['email'] = $ut;

                    $ua['name'] = 'test';

                    $users[$key] = (object)$ua;
                }
            }

            try{
                Mail::to($users)->send(new WishlistReqeustMail($user->email, $wish));
            }
            catch (\Exception $e){
            }

            if($request->hasFile('attachments')){
                $files = $request->file('attachments');
                foreach ($files as $file){
                    $name = Str::slug($file->getClientOriginalName());
                    $attachement = date("mmYhisa_") . $name;
                    $file->move(public_path() . '/wishlist-attachments/', $attachement);
                    $wa = new WishlistAttachment();
                    $wa->source = $attachement;
                    $wa->wishlist_id = $wish->id;
                    $wa->save();
                }
            }
            $this->log->store($wish->user_id, 'Wishlist', $wish->id, $wish->product_name,  'Wishlist Created');




            return redirect()->back()->with('success','Wishlist created successfully!');

        }

        else{
            return redirect()->back()->with('error','Associated Manager Not Found');
        }
    }

    public function create_wishlist_thread(Request $request){

        $manager = User::find($request->input('manager_id'));
        $wish = Wishlist::find($request->input('wishlist_id'));
        if($manager != null && $wish != null){
            $thread = new WishlistThread();
            $thread->reply = $request->input('reply');
            $thread->source = $request->input('source');
            $thread->manager_id = $manager->id;
            $thread->user_id = $request->input('user_id');
            $thread->shop_id = $request->input('shop_id');
            $thread->wishlist_id = $request->input('wishlist_id');

            if(isset($request->show_flag)) {
                $thread->show = true;
            }

            $thread->save();

            $wish->updated_at = now();
            $wish->save();

            if($request->hasFile('attachments')){
                $files = $request->file('attachments');
                foreach ($files as $file){
                    $name = Str::slug($file->getClientOriginalName());
                    $attachement = date("mmYhisa_") . $name;
                    $file->move(public_path() . '/wishlist-attachments/', $attachement);
                    $ta = new WishlistAttachment();
                    $ta->source = $attachement;
                    $ta->thread_id = $thread->id;
                    $ta->save();
                }
            }
            if($request->input('source') == 'manager') {
                $tl = new ManagerLog();
                $tl->message = 'A Reply Added By Manager on Wishlist at ' . date_create($thread->created_at)->format('d M, Y h:i a');
                $tl->status = "Reply From Manager";
                $tl->manager_id = $manager->id;
                $tl->save();
                $this->notify->generate('Wish-list','Wishlist Thread','You have a new message from wishlist named '.$wish->product_name,$wish);
            }

            return redirect()->back()->with('success','Reply sent successfully!');

        }

        else{
            return redirect()->back()->with('error','Associated Manager Not Found');
        }
    }
    public function approve_wishlist(Request $request){
        $manager = User::find($request->input('manager_id'));
        $wish = Wishlist::find($request->input('wishlist_id'));
        if($manager != null && $wish != null){
            $wish->status_id = 2;
            $wish->approved_price = $request->input('approved_price');
            $wish->updated_at = now();
            $wish->save();
            $tl = new ManagerLog();
            $tl->message = 'Manager Approved Wishlist against price '.number_format($wish->approved_price,2).' at ' . date_create($wish->updated_at)->format('d M, Y h:i a');
            $tl->status = "Manager Approved Wishlist";
            $tl->manager_id = $manager->id;
            $tl->save();

            $user = $wish->has_user;
            try{
                Mail::to($user->email)->send(new WishlistApproveMail($user, $wish));
            }
            catch (\Exception $e){
            }

            $this->notify->generate('Wish-list','Wishlist Approved','Wishlist named '.$wish->product_name.' has been approved by your manager',$wish);
            $this->log->store(0, 'Wishlist', $wish->id, $wish->product_name, 'Wishlist Approved');


            return redirect()->back()->with('success','Wishlist Approved Successfully!');

        }

        else{
            return redirect()->back()->with('error','Associated Manager Not Found');
        }
    }
    public function reject_wishlist(Request $request){
        $manager = User::find($request->input('manager_id'));
        $wish = Wishlist::find($request->input('wishlist_id'));
        if($manager != null && $wish != null){
            $wish->status_id = 4;
            $wish->reject_reason = $request->input('reject_reason');
            $wish->updated_at = now();
            $wish->save();
            $tl = new ManagerLog();
            $tl->message = 'Manager Rejected Wishlist against price '.number_format($wish->cost,2).' at ' . date_create($wish->updated_at)->format('d M, Y h:i a');
            $tl->status = "Manager Rejected Wishlist";
            $tl->manager_id = $manager->id;
            $tl->save();
            $this->notify->generate('Wish-list','Wishlist Rejected','Wishlist named '.$wish->product_name.' has been rejected by your manager',$wish);

            $user = $wish->has_user;
            try{
                Mail::to($user->email)->send(new WishlistRejectMail($user, $wish));
            }
            catch (\Exception $e){
            }
            $this->log->store(0, 'Wishlist', $wish->id, $wish->product_name, 'Wishlist Rejected');

            return redirect()->back()->with('success','Wishlist Rejected Successfully!');

        }

        else{
            return redirect()->back()->with('error','Associated Manager Not Found');
        }
    }
    public function accept_wishlist(Request $request){
        $manager = User::find($request->input('manager_id'));
        $wish = Wishlist::find($request->input('wishlist_id'));
        if($manager != null && $wish != null){
            if($request->has('has_product')){
                $wish->has_store_product = 1;
                $wish->product_shopify_id = $request->input('product_shopify_id');
            }
            $wish->status_id = 3;
            $wish->updated_at = now();
            $wish->save();
            $this->notify->generate('Wish-list','Wishlist Accepted','Wishlist named '.$wish->product_name.' has been accepted',$wish);

            $this->log->store($wish->user_id, 'Wishlist', $wish->id, $wish->product_name, 'Wishlist Accepted');

            return redirect()->back()->with('success','Wishlist Accepted Successfully!');
        }

        else{
            return redirect()->back()->with('error','Associated Manager Not Found');
        }
    }

    public function completed_wishlist(Request $request){
        $manager = User::find($request->input('manager_id'));
        $wish = Wishlist::find($request->input('wishlist_id'));
        if($manager != null && $wish != null){
            if($wish->has_store_product == 1){
                if($this->check_product($wish,$request->input('product_shopify_id'))){
                    $response = $this->fetch_product($wish,$request->input('product_shopify_id'));
                    if(!$response->errors){
                        $categories = Category::latest()->get();
                        $platforms = WarnedPlatform::all();
                        $shops = Shop::whereNotIn('shopify_domain',['wefullfill.myshopify.com'])->get();
                        if(Auth::user()->email == 'wordpress_admin@wefullfill.com'){
                            return view('setttings.wishlist.map_product')->with([
                                'product' => $response->body->product,
                                'wishlist' => $wish,
                                'product_shopify_id' => $request->input('product_shopify_id'),
                                'categories' => $categories,
                                'platforms' => $platforms,
                                'shops' => $shops
                            ]);

                        }
                        else{
                            return view('sales_managers.wishlist.map_product')->with([
                                'product' => $response->body->product,
                                'wishlist' => $wish,
                                'product_shopify_id' => $request->input('product_shopify_id'),
                                'categories' => $categories,
                                'platforms' => $platforms,
                                'shops' => $shops
                            ]);
                        }

                    }
                    else{
                        return redirect()->back()->with('error','Wishlist cant be completed because user enter shopify id doesnt belong to any product!');
                    }

                }
                else{
                    return redirect()->back()->with('error','Wishlist cant be completed because user enter shopify id doesnt belong to any product!');
                }


            }
            else{
                $wish->status_id = 5;
                $wish->related_product_id = $request->input('link_product_id');
                $wish->updated_at = now();
                $wish->save();

                $this->notify->generate('Wish-list','Wishlist Completed','Wishlist named '.$wish->product_name.' has been completed',$wish);

                return redirect()->back()->with('success','Wishlist Completed Successfully!');
            }

        }

        else{
            return redirect()->back()->with('error','Associated Manager Not Found');
        }
    }

    public function check_product(Wishlist $wishlist,$shopify_product_id){
        $response = $this->fetch_product($wishlist, $shopify_product_id);
        if(!$response->errors){
            return 1;
        }
        else{
            return 0;
        }
    }

    public function map_product(Request $request){

        $wish = Wishlist::find($request->input('wishlist_id'));
        $response = $this->fetch_product($wish,$request->input('product_shopify_id'));
        if($wish !=null ){
            if(!$response->errors){
                /*Create and Synced Product to Admin*/
                $product =  $this->create_sync_product_to_admin($request, $response);
                /*Import Product to requested store*/

                $related_product_id = $this->import_to_store($wish,$request->input('product_shopify_id'),$product->id);
                $wish->status_id = 5;
                $wish->related_product_id = $related_product_id;
                $wish->updated_at = now();
                $wish->save();


                $user = $wish->has_user;
                try{
                    Mail::to($user->email)->send(new WishlistComplateMail($user, $wish));
                }
                catch (\Exception $e){
                }

                $this->notify->generate('Wish-list','Wishlist Completed','Wishlist named '.$wish->product_name.' has been completed',$wish);


                if(Auth::user()->email == 'wordpress_admin@wefullfill.com'){
                    $this->log->store(0, 'Wishlist', $wish->id, $wish->product_name, 'Wishlist Completed');
                    return redirect()->route('wishlist.index')->with('success','Wishlist Completed Successfully!');
                }
                else{
                    $this->log->store($wish->manager_id, 'Wishlist', $wish->id, $wish->product_name, 'Wishlist Completed');
                    return redirect()->route('sales_managers.wishlist')->with('success','Wishlist Completed Successfully!');
                }

            }
            else{
                if(Auth::user()->email == 'wordpress_admin@wefullfill.com'){
                    return redirect()->route('wishlist.index')->with('errors','Product Not Found on respective store, cant complete the wishlist process!');

                }
                else{
                    return redirect()->route('sales_managers.wishlist')->with('errors','Product Not Found on respective store, cant complete the wishlist process!');

                }

            }
        }
        else{
            if(Auth::user()->email == 'wordpress_admin@wefullfill.com'){
                return redirect()->route('wishlist.index')->with('errors','Wishlist Not Found!');

            }
            else{
                return redirect()->route('sales_managers.wishlist')->with('errors','Wishlist Not Found!');


            }
        }
    }

    public function import_to_store(Wishlist $wishlist,$shopify_product_id,$linked_product_id){
        $response = $this->fetch_product($wishlist, $shopify_product_id);
        if(!$response->errors){
            $product = $response->body->product;
            return $this->map_to_retailer_product($wishlist,$response,$product,$linked_product_id);
        }
        else{
            return null;
        }


    }


    public function map_to_retailer_product(Wishlist $wishlist, $response, $product,$linked_product_id)
    {

        if (RetailerProduct::where('shopify_id', $product->id)->exists()) {
            $retailerProduct = RetailerProduct::where('shopify_id', $product->id)->first();
        } else {
            $retailerProduct = new RetailerProduct();
        }




        $retailerProduct->shopify_id = $product->id;
        $retailerProduct->title = $product->title;
        $retailerProduct->description = $product->body_html;
        $retailerProduct->type = $product->product_type;
        $retailerProduct->tags = $product->tags;
        $retailerProduct->vendor = $product->vendor;
        $admin_product = Product::find($linked_product_id);

        if($admin_product) {
            $retailerProduct->price = $admin_product->price;
            $retailerProduct->cost = $admin_product->price;
        }
        else {
            $retailerProduct->price = $wishlist->approved_price;
            $retailerProduct->cost = $wishlist->approved_price;
        }


        if (count($product->variants) > 0) {
            $retailerProduct->variants = 1;
        }
        $retailerProduct->status = 1;
        $retailerProduct->fulfilled_by = 'Fantasy';
        $retailerProduct->toShopify = 1;
        $retailerProduct->shop_id = $wishlist->shop_id;
        $retailerProduct->user_id = $wishlist->user_id;
        $retailerProduct->import_from_shopify = 1;
        $retailerProduct->save();

        /*Product Images SYNC*/
        if (count($product->images) > 0) {
            foreach ($product->images as $index => $image) {
                $retailerProductImage = new RetailerImage();
                if (count($image->variant_ids) > 0) {
                    $retailerProductImage->isV = 1;
                } else {
                    $retailerProductImage->isV = 0;
                }
                $retailerProductImage->shopify_id = $image->id;
                $retailerProductImage->product_id = $retailerProduct->id;
                $retailerProductImage->shop_id = $wishlist->shop_id;
                $retailerProductImage->image = $image->src;
                $retailerProductImage->position = $image->position;
                $retailerProductImage->save();
            }
        }
        /*Product Variants SYNC*/

        if (count($product->variants) > 0) {

            foreach ($product->variants as $index => $variant) {
                $retailerProductVariant = new RetailerProductVariant();
                $retailerProductVariant->shopify_id = $variant->id;
                $retailerProductVariant->title = $variant->title;
                $retailerProductVariant->option1 = $variant->option1;
                $retailerProductVariant->option2 = $variant->option2;
                $retailerProductVariant->option3 = $variant->option3;

                if($admin_product) {
                    $retailerProductVariant->price = $admin_product->price;
                    $retailerProductVariant->cost = $admin_product->price;
                }
                else {
                    $retailerProductVariant->price = $wishlist->approved_price;
                    $retailerProductVariant->cost = $wishlist->approved_price;
                }

                $retailerProductVariant->quantity = $variant->inventory_quantity;
                $retailerProductVariant->sku = $variant->sku;
                $retailerProductVariant->barcode = $variant->barcode;
                $retailerProductVariant->product_id = $retailerProduct->id;
                $retailerProductVariant->shop_id = $wishlist->shop_id;

                if ($variant->image_id != null) {
                    $image_linked = $retailerProduct->has_images()->where('shopify_id', $variant->image_id)->first();
                    $retailerProductVariant->image = $image_linked->id;
                }

                if ($index == 0) {
                    $retailerProduct->quantity = $variant->inventory_quantity;
                    $retailerProduct->weight = $variant->weight;
                    $retailerProduct->sku = $variant->sku;
                    $retailerProduct->barcode = $variant->barcode;
                    $retailerProduct->save();
                }

                $retailerProduct->linked_product_id = $linked_product_id;
                $retailerProduct->save();

                $retailerProductVariant->save();

                $shop = $this->helper->getSpecificLocalShop($retailerProduct->shop_id);
                if($shop != null){
                    if(!in_array($linked_product_id,$shop->has_imported->pluck('id')->toArray())){
                        $shop->has_imported()->attach([$linked_product_id]);
                    }
                }
                /*Shop-User Import Relation*/
                if(count($this->helper->getSpecificLocalShop($retailerProduct->shop_id)->has_user) > 0){
                    $user = $this->helper->getSpecificLocalShop($retailerProduct->shop_id)->has_user[0];
                    if(!in_array($linked_product_id,$user->has_imported->pluck('id')->toArray())){
                        $user->has_imported()->attach([$linked_product_id]);
                    }
                }

                $i = [
                    'variant' => [
                        "fulfillment_service" => "wefullfill",
                        'inventory_management' => 'wefullfill',
                    ]
                ];
                $s = $this->helper->getSpecificShop($retailerProduct->shop_id);
                $s->api()->rest('PUT', '/admin/api/2019-10/variants/' . $variant->id .'.json', $i);
            }

        }

        return $retailerProduct->id;
    }

    /**
     * @param Wishlist $wishlist
     * @param $shopify_product_id
     * @return mixed
     */
    public function fetch_product(Wishlist $wishlist, $shopify_product_id)
    {
        $shop = $this->helper->getSpecificShop($wishlist->has_store->id);
        $response = $shop->api()->rest('GET', '/admin/api/2019-10/products/' . $shopify_product_id . '.json');
        return $response;
    }

    public function ProductVariants($admin_product, $shopify_product, $data, $id)
    {
        for ($i = 0; $i < count($data->variant_title); $i++) {
            $options = explode('/', $data->variant_title[$i]);
            $variants = new  ProductVariant();
            if (!empty($options[0])) {
                $variants->option1 = $options[0];
            }
            if (!empty($options[1])) {
                $variants->option2 = $options[1];
            }
            if (!empty($options[2])) {
                $variants->option3 = $options[2];
            }
            $variants->title = $data->variant_title[$i];
            $variants->price = $data->variant_price[$i];
            $variants->compare_price = $data->variant_comparePrice[$i];
            $variants->quantity = $data->variant_quantity[$i];
            $variants->cost = $data->variant_cost[$i];
            $variants->sku = $data->variant_sku[$i];
            $variants->barcode = $data->variant_barcode[$i];
            $variants->product_id = $id;
            $variants->save();

            if(count($shopify_product->variants) > 0) {
                if ($shopify_product->variants[$i]->image_id != null) {
                    $image_linked = $admin_product->has_images()->where('shopify_id', $shopify_product->variants[$i]->image_id)->first();
                    if($image_linked != null) {
                        $variants->image = $image_linked->id;
                        $variants->save();
                    }
                }
            }

        }
    }

    public function variants_template_array($product){

        $prod = Product::where('title', $product->title)->first();


        $variants_array = [];
        foreach (ProductVariant::where('product_id', $prod->id)->get() as $index => $varaint) {


            if ($varaint->has_image != null) {
                $image_id = $varaint->has_image->shopify_id;
            }
            else {
                $image_id = null;
            }

            array_push($variants_array, [
                'title' => $varaint->title,
                'sku' => $varaint->sku,
                'option1' => $varaint->option1,
                'option2' => $varaint->option2,
                'option3' => $varaint->option3,
//                'inventory_quantity' => $varaint->quantity,
//                'inventory_management' => 'shopify',
                'inventory_quantity' => $varaint->quantity,
                "fulfillment_service" => "wefullfill",
                'inventory_management' => 'wefullfill',
                'grams' => $prod->weight * 1000,
                'weight' => $prod->weight,
                'weight_unit' => 'kg',
                'barcode' => $varaint->barcode,
                'price' => $varaint->price,
                'cost' => $varaint->cost,
//                'image_id' => $image_id,
            ]);
        }
        return $variants_array;
    }

    public function options_template_array($product){
        $prod = Product::where('title', $product->title)->first();
        $options_array = [];
        if (count($prod->option1($prod)) > 0) {
            $temp = [];
            foreach ($prod->option1($prod) as $a) {
                array_push($temp, $a);
            }
            array_push($options_array, [
                'name' => 'Option1',
                'position' => '1',
                'values' => json_encode($temp),
            ]);
        }
        if (count($prod->option2($prod)) > 0) {
            $temp = [];
            foreach ($prod->option2($prod) as $a) {
                array_push($temp, $a);
            }
            array_push($options_array, [
                'name' => 'Option2',
                'position' => '2',
                'values' => json_encode($temp),
            ]);
        }
        if (count($prod->option3($prod)) > 0) {
            $temp = [];
            foreach ($prod->option3($prod) as $a) {
                array_push($temp, $a);
            }
            array_push($options_array, [
                'name' => 'Option3',
                'position' => '3',
                'values' => json_encode($temp),
            ]);
        }
        return $options_array;
    }

    /**
     * @param Request $request
     * @param $response
     * @return Product
     */
    public function create_sync_product_to_admin_old(Request $request, $response): Product
    {
        $product = new Product();
        $product->title = $request->title;
        $product->description = $request->description;
        $product->price = $request->price;
        $product->compare_price = $request->compare_price;
        $product->cost = $request->cost;
        $product->type = $request->product_type;
        $product->vendor = $request->vendor;
        $product->tags = $request->tags;
        $product->quantity = $request->quantity;
        $product->weight = $request->weight;
        $product->sku = $request->sku;
        $product->barcode = $request->barcode;
        $product->fulfilled_by = $request->input('fulfilled-by');
        $product->status = $request->input('status');
        $product->processing_time = $request->input('processing_time');

        if ($request->variants) {
            $product->variants = $request->variants;
        }

        $product->save();
        if ($request->category) {
            $product->has_categories()->attach($request->category);
        }
        if ($request->sub_cat) {
            $product->has_subcategories()->attach($request->sub_cat);
        }
        if ($request->platforms) {
            $product->has_platforms()->attach($request->platforms);
        }
        $product->save();

        $count_product_images = count($product->has_images);


        $shopify_product = $response->body->product;
        foreach ($shopify_product->images as $index => $img) {
            $image = file_get_contents($img->src);
            $filename = now()->format('YmdHi') . $request->input('title') . rand(12321, 456546464) . '.jpg';
            file_put_contents(public_path('images/' . $filename), $image);
            $image = new Image();
            $image->isV = 0;
            $image->position = $index + 1 + $count_product_images;
            $image->product_id = $product->id;
            $image->shopify_id = $img->id;
            $image->image = $filename;
            $image->save();
        }


        if ($request->variants) {
            $this->ProductVariants($product, $shopify_product, $request, $product->id);
        }

        $product->global = $request->input('global');
        $product->save();

        if($request->input('global') == 0 && $request->has('shops') && count($request->input('shops')) > 0){
            $product->has_preferences()->attach($request->input('shops'));
        }

        if ($request->hasFile('images')) {

            foreach ($request->file('images') as $index => $image) {

                $destinationPath = 'images/';
                $filename = now()->format('YmdHi') . str_replace([' ', '(', ')'], '-', $image->getClientOriginalName());
                $image->move($destinationPath, $filename);
                $image = new Image();
                $image->isV = 0;
                $image->product_id = $product->id;
                $image->position = $index + 1;
                $image->image = $filename;
                $image->save();
            }

        }

        $prod = Product::where('title', $request->title)->first();

        $this->woocommerce_helper->import_to_woocommerce($prod->id);

        /*Import to WeFullFill Woccommerce Store*/
        $variants_array = [];
        $options_array = [];
        $images_array = [];
        //converting variants into shopify api format
        $variants_array = $this->variants_template_array($product, $variants_array);

        /*Product Options*/
        $options_array = $this->options_template_array($product, $options_array);
        /*Product Images*/


        foreach ($prod->has_images as $index => $image) {
            if ($image->isV == 0) {
                $src = asset('images') . '/' . $image->image;
            } else {
                $src = asset('images/variants') . '/' . $image->image;
            }
            array_push($images_array, [
                'position' => $index + 1,
                'src' => $src,
            ]);
        }


        $shop = $this->helper->getAdminShop();
        /*Categories and Subcategories*/
        $tags = $product->tags;
        if (count($product->has_categories) > 0) {
            $categories = implode(',', $product->has_categories->pluck('title')->toArray());
            $tags = $tags . ',' . $categories;
        }
        if (count($product->has_subcategories) > 0) {
            $subcategories = implode(',', $product->has_subcategories->pluck('title')->toArray());
            $tags = $tags . ',' . $subcategories;
        }
        if ($product->status == 1) {
            $published = true;
        } else {
            $published = false;
        }

        $productdata = [
            "product" => [
                "title" => $product->title,
                "body_html" => $product->description,
                "vendor" => $product->vendor,
                "images" => $images_array,
                "tags" => $tags,
                "product_type" => $product->type,
                "variants" => $variants_array,
                "options" => $options_array,
                "published" => $published
            ]
        ];

        $response = $shop->api()->rest('POST', '/admin/products.json', $productdata);
        $product_shopify_id = $response->body->product->id;
        $product->shopify_id = $product_shopify_id;
        $price = $product->price;
        $product->save();

        $shopifyImages = $response->body->product->images;
        $shopifyVariants = $response->body->product->variants;

        if (count($prod->hasVariants) == 0) {
            $variant_id = $shopifyVariants[0]->id;
            $product->inventory_item_id =$shopifyVariants[0]->inventory_item_id;
            $product->save();
            $i = [
                'variant' => [
                    'price' => $price
                ]
            ];
            $shop->api()->rest('PUT', '/admin/api/2019-10/variants/' . $variant_id . '.json', $i);

            $data = [
                "inventory_item" => [
                    'id' => $product->inventory_item_id,
                    "tracked" => true
                ]

            ];
            $resp = $shop->api()->rest('PUT', '/admin/api/2020-07/inventory_items/' . $product->inventory_item_id . '.json', $data);
            /*Connect to Wefullfill*/
            $data = [
                'location_id' => 46023344261,
                'inventory_item_id' => $product->inventory_item_id,
                'relocate_if_necessary' => true
            ];
            $res = $shop->api()->rest('POST', '/admin/api/2020-07/inventory_levels/connect.json', $data);
            /*Set Quantity*/

            $data = [
                'location_id' => 46023344261,
                'inventory_item_id' => $product->inventory_item_id,
                'available' => $product->quantity,

            ];

            $res = $shop->api()->rest('POST', '/admin/api/2020-07/inventory_levels/set.json', $data);
        }

        foreach ($prod->hasVariants as $index => $v) {
            $v->shopify_id = $shopifyVariants[$index]->id;
            $v->inventory_item_id =$shopifyVariants[$index]->inventory_item_id;
            $v->save();
        }
        foreach ($prod->has_platforms as $index => $platform) {
            $index = $index + 1;
            $productdata = [
                "metafield" => [
                    "key" => "warned_platform" . $index,
                    "value" => $platform->name,
                    "value_type" => "string",
                    "namespace" => "platform"
                ]
            ];
            $resp = $shop->api()->rest('POST', '/admin/api/2019-10/products/' . $product_shopify_id . '/metafields.json', $productdata);
        }

        if (count($shopifyImages) == count($prod->has_images)) {
            foreach ($prod->has_images as $index => $image) {
                $image->shopify_id = $shopifyImages[$index]->id;
                $image->save();
            }
        }

        foreach ($prod->hasVariants as $index => $v) {
            if ($v->has_image != null) {
                $i = [
                    'image' => [
                        'id' => $v->has_image->shopify_id,
                        'variant_ids' => [$v->shopify_id],
//                        'src' => $v->has_image->image
                    ]
                ];
                $imagesResponse = $shop->api()->rest('PUT', '/admin/api/2019-10/products/' . $product_shopify_id . '/images/' . $v->has_image->shopify_id . '.json', $i);
            }
        }
        return $product;
    }

    public function create_sync_product_to_admin(Request $request, $response): Product
    {
        $product = new Product();
        $product->title = $request->title;
        $product->description = $request->description;
        $product->price = $request->price;
        $product->compare_price = $request->compare_price;
        $product->cost = $request->cost;
        $product->type = $request->product_type;
        $product->vendor = $request->vendor;
        $product->tags = $request->tags;
        $product->quantity = $request->quantity;
        $product->weight = $request->weight;
        $product->sku = $request->sku;
        $product->barcode = $request->barcode;
        $product->fulfilled_by = $request->input('fulfilled-by');
        $product->status = $request->input('status');
        $product->processing_time = $request->input('processing_time');

        $attributes = $response->body->product->options;
        if (!empty($attributes[0])) {
            $product->attribute1 = $attributes[0]->name;
        }
        if (!empty($attributes[1])) {
            $product->attribute2 = $attributes[1]->name;
        }
        if (!empty($attributes[2])) {
            $product->attribute3 = $attributes[2]->name;
        }

        $product->save();

        if ($request->variants) {
            $product->variants = $request->variants;
        }

        $product->save();
        if ($request->category) {
            $product->has_categories()->attach($request->category);
        }
        if ($request->sub_cat) {
            $product->has_subcategories()->attach($request->sub_cat);
        }
        if ($request->platforms) {
            $product->has_platforms()->attach($request->platforms);
        }
        $product->save();

        if($request->tags) {
            $tags = explode(',', $request->tags);

            foreach($tags as $tag) {
                if(Tag::where('name', $tag)->exists()) {
                    $t = Tag::where('name', $tag)->first();
                }
                else{
                    $t = new Tag();
                    $t->name = $tag;
                    $t->save();

                    $woocommerce = $this->helper->getWooCommerceAdminShop();
                    $res = $woocommerce->post('products/tags', ['name' => $t->name]);
                    $t->woocommerce_id = $res->id;
                    $t->save();
                }
                $product->tags()->attach($t->id);
            }

        }

        $count_product_images = count($product->has_images);


        $shopify_product = $response->body->product;
        foreach ($shopify_product->images as $index => $img) {
            $image = file_get_contents($img->src);
            $filename = now()->format('YmdHi') . $request->input('title') . rand(12321, 456546464) . '.jpg';
            file_put_contents(public_path('images/' . $filename), $image);
            $image = new Image();
            $image->isV = 0;
            $image->position = $index + 1 + $count_product_images;
            $image->product_id = $product->id;
            $image->shopify_id = $img->id;
            $image->image = $filename;
            $image->save();
        }


        if ($request->variants) {
            $this->ProductVariants($product, $shopify_product, $request, $product->id);
        }

        $product->global = $request->input('global');
        $product->save();

        if($request->input('global') == 0 && $request->has('shops') && count($request->input('shops')) > 0){
            $product->has_preferences()->attach($request->input('shops'));
        }

        if ($request->hasFile('images')) {

            foreach ($request->file('images') as $index => $image) {

                $destinationPath = 'images/';
                $filename = now()->format('YmdHi') . str_replace([' ', '(', ')'], '-', $image->getClientOriginalName());
                $image->move($destinationPath, $filename);
                $image = new Image();
                $image->isV = 0;
                $image->product_id = $product->id;
                $image->position = $index + 1;
                $image->image = $filename;
                $image->save();
            }

        }


        $product = Product::where('title', $request->title)->first();
        $woocommerce = $this->helper->getWooCommerceAdminShop();

        /*Product Attributes*/
        $attributes_array = $this->woocommerce_helper->attributes_template_array($product);

        /*Product Dimensions*/
        $dimension_array = array(
            'width' => is_null($product->width) ? "0" : $product->width,
            'height' => is_null($product->height) ? "0" : $product->height,
            'length' => is_null($product->length) ? "0" : $product->length
        );

        /*Product Images*/
        $images_array = [];
        foreach ($product->has_images as $index => $image) {
            if ($image->isV == 0) {
                $src = asset('images') . '/' . $image->image;
            } else {
                $src = asset('images/variants') . '/' . $image->image;
            }
            array_push($images_array, [
                'alt' => $product->title . '_' . $index,
                'name' => $product->title . '_' . $index,
                'src' => $src,
            ]);
        }

        /*Tags*/
        $tags_array = [];
        if($product->tags()->count() > 0) {
            foreach ($product->tags()->get() as $tag) {
                array_push($tags_array, [
                    'id' => $tag->woocommerce_id,
                ]);
            }
        }

        /*Categories*/
        $categories_array = [];

        if(count($product->has_categories) > 0){
            $product_categories = $product->has_categories->pluck('woocommerce_id')->toArray();

            foreach ($product_categories as $category) {
                array_push($categories_array, [
                    'id' => $category,
                ]);
            }
        }

        /*SubCategories*/
        if(count($product->has_subcategories) > 0) {
            $product_sub_categories = $product->has_subcategories->pluck('woocommerce_id')->toArray();
            foreach ($product_sub_categories as $category) {
                array_push($categories_array, [
                    'id' => $category,
                ]);
            }
        }

        /*Platfroms*/
        $meta_data_array = [];
        $platforms = null;
        if(count($product->has_platforms) > 0) {
            foreach ($product->has_platforms as $index => $platform){
                $platforms = $platforms . $platform->name . ',';
            }
        }

        array_push($meta_data_array,[
            "key" => "warned_platform",
            "value"=> $platforms,
        ]);

        if($product->status == 1)
            $published = 'publish';
        else
            $published = 'draft';


        if($product->variants == 1)
            $product_type = 'variable';
        else
            $product_type = 'simple';


        $productdata = [
            "name" => $product->title,
            "description" => $product->description,
            "short_description" => $product->short_description,
            "slug" => $product->slug,
            "tags" => $tags_array,
            "type" => $product_type,
            "attributes" => $attributes_array,
            "images" => $images_array,
            "published"=>  $published,
            "sale_price" => $product->price,
            "regular_price" => $product->price,
            "sku" => $product->sku,
            "weight" => $product->weight,
            "manage_stock" => true,
            "stock_quantity" => $product->quantity,
            "dimensions" => $dimension_array,
            "categories" => $categories_array,
            "meta_data" => $meta_data_array
        ];

        /*Creating Product On Woocommerce*/
        $res = $woocommerce->post('products', $productdata);

        $product_woocommerce_id =  $res->id;
        $product->woocommerce_id = $product_woocommerce_id;
        $product->to_woocommerce = 1;
        $product->save();

        $woocommerce_images = $res->images;

        if (count($woocommerce_images) == count($product->has_images)) {
            foreach ($product->has_images as $index => $image) {
                $image->woocommerce_id = $woocommerce_images[$index]->id;
                $image->save();
            }
        }

        if($product->variants == 1) {
            $variants_array =  $this->woocommerce_helper->woocommerce_variants_template_array($product);

            $variantdata = [
                'create' => $variants_array
            ];

            /*Creating Product Variations On Woocommerce*/
            $res = $woocommerce->post("products/".$product_woocommerce_id."/variations/batch", $variantdata);

            $woocommerce_variants = $res->create;
            foreach ($product->hasVariants as $index => $v){
                $v->woocommerce_id = $woocommerce_variants[$index]->id;
//                $v->inventory_item_id = $shopifyVariants[$index]->inventory_item_id;
                $v->save();
            }
        }

        $this->log->store(0, 'Product', $product->id, $product->title, 'Product Imported To Woocommerce');

        return $product;
    }


    public function delete_wishlist($id){
        Wishlist::find($id)->delete();
        if(WishlistAttachment::where('wishlist_id', $id)->count() >= 1){
            WishlistAttachment::where('wishlist_id', $id)->delete();
        }
        if(WishlistCountry::where('wishlist_id', $id)->count() >= 1){
            WishlistCountry::where('wishlist_id', $id)->delete();
        }
        if(WishlistThread::where('wishlist_id', $id)->count() >= 1){
            WishlistThread::where('wishlist_id', $id)->delete();
        }
        return redirect()->back()->with('success', 'Wishlist deleted successfully');
    }


//    public function manuallyChangeInventoryManager() {
//        $shop = $this->helper->getSpecificShop(55);
//
//        $ids = [1718761553983,1966545109055,1760465453119];
//
//        foreach ($ids as $id) {
//            $response = $shop->api()->rest('GET', '/admin/api/2019-10/products/'. $id .'.json');
//            $product = $response->body->product;
//            dd($product);
//            if (count($product->variants) > 0) {
//                foreach ($product->variants as $index => $variant) {
//                    $i = [
//                        'variant' => [
//                            "fulfillment_service" => "wefullfill",
//                            'inventory_management' => 'wefullfill',
//                        ]
//                    ];
//                    $response = $shop->api()->rest('PUT', '/admin/api/2019-10/variants/' . $variant->id . '.json', $i);
//                    if ($response->errors) {
//                        dd($response);
//                    }
//                }
//            }
//        }
//    }
//    public function test() {
//
//        $shop = $this->helper->getSpecificShop(55);
//        $response = $shop->api()->rest('GET', '/admin/api/2019-10/products/5776107307163.json');
//
//        $products = $response->body->product;
//        dd($products);
//
//    }

}
