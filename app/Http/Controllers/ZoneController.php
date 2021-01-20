<?php

namespace App\Http\Controllers;

use App\Country;
use App\Courier;
use App\Product;
use App\ShippingRate;
use App\Zone;
use Illuminate\Http\Request;

class ZoneController extends Controller
{
   public function index(){
      $countries = Country::all();
       $zones =  Zone::query();
       return view('setttings.zones.index')->with([
           'zones'=>$zones->orderBy('created_at','DESC')->get(),
           'countries' =>$countries
       ]);
   }

   public function create(Request $request){
//       dd($request);
       $zone = Zone::create([
          'name' => $request->input('name')
       ]);
       $zone->has_countries()->attach($request->input('countries'));
       return redirect()->back()->with('success','Zone Successfully Generated!');

   }
    public function rate_create(Request $request){


        ShippingRate::create($request->all());

        return redirect()->back()->with('success','Rate Successfully Generated!');
    }
    public function update(Request $request){
//       dd($request);
        $zone = Zone::find($request->id);
        $zone->name = $request->input('name');
        $zone->save();
        $zone->has_countries()->sync($request->input('countries'));
        return redirect()->back()->with('success','Zone Successfully Updated!');
    }
    public function delete(Request $request){
        $zone = Zone::find($request->id);
        $zone->has_countries()->detach();
        if(count($zone->has_rate) > 0) {
            foreach ($zone->has_rate as $rate) {
                $rate->delete();
            }
        }
        $zone->delete();
        return redirect()->back()->with('success','Zone Successfully Deleted!');

    }
    public function rate_update(Request $request){

        $this->validate($request, [
            'title' => 'required'
        ]);

        if(Courier::where('zone_id', $request->zone_id)->exists()) {
            $courier =  Courier::where('zone_id', $request->zone_id)->first();
        }
        else {
            $courier = new Courier();
        }

        $courier->title =  $request->title;
        $courier->url =  $request->url;
        $courier->zone_id = $request->zone_id;
        $courier->save();


        ShippingRate::find($request->id)->update($request->all());
        return redirect()->back()->with('success','Rate Successfully Updated!');
    }
    public function rate_delete(Request $request){
        ShippingRate::find($request->id)->delete();
        return redirect()->back()->with('success','Rate Successfully Deleted!');
    }

    public function getShippingRates(Request $request){
       $country = $request->input('country');
       $product = Product::where('shopify_id',$request->input('product_id'))->first();
       if($product != null){
           $total_weight = $product->weight;

           $zoneQuery = Zone::query();
         $zoneQuery->whereHas('has_countries',function ($q) use ($country){
             $q->where('name','LIKE','%'.$country.'%');
         });
        $zones = $zoneQuery->get();
             $message = null;
        if(count($zones) > 0){
            foreach ($zones as $zone) {
                if($zone->has_rate != null){
                    if (count($zone->has_rate) > 0) {

                        if($zone->has_rate[0]->shipping_price > 0){
                            if($zone->has_rate[0]->type == 'flat'){
                                $message = ' <prp_up> $' . number_format($zone->has_rate[0]->shipping_price, 2) . '</prp_up>';

                            }
                            else{
                                if($zone->has_rate[0]->min > 0){
                                    $ratio = $total_weight/$zone->has_rate[0]->min;
                                    $new_shipping_price = '$'.number_format($zone->has_rate[0]->shipping_price*$ratio,2);
                                    $message = ' <prp_up> ' . $new_shipping_price . '</prp_up>';

                                }
                                else{
                                $message = ' <prp_up> Free Shipping</prp_up>';

                                }
                            }
                        }
                        else{
                            $message = '<prp_up> Free Shipping </prp_up>';
                        }

                        if($product->processing_time != null){
                            $message_3 = '<prp_up>'.$product->processing_time.'</prp_up>';
                        }
                        else{
                            $message_3 = '<prp_up>'.$zone->has_rate[0]->processing_time.'</prp_up>';
                        }
                        $message_2 = '<prp_up>'.$zone->has_rate[0]->shipping_time.'</prp_up>';
                        break;
                    }
                }

            }
            if($message == null){
                $message = "No Shipping Carrier Available For ".$country." Right Now!";
                $message_2 = "Delivery Time Not Available Right Now!";
                $message_3 = "Processing Time Not Found For This Product!";

                return response()->json([
                    'status' => 'no-zone-found',
                    'message' => $message,
                    'message_2' => $message_2,
                    'message_3' => $message_3
                ]);
            }

            $rates = view('inc.shipping_rates',[
                'zones' =>$zones
            ])->render();

            return response()->json([
                'status' => 'zones-found',
                'message' => $message,
                'message_2' => $message_2,
                'message_3' => $message_3,
                'rates' => $rates
            ]);


        }
        else{
            $message = "No Shipping Carrier Available For ".$country;
            $message_2 = "Delivery Time Not Available Right Now!";
            $message_3 = "Processing Time Not Found For This Product!";
            return response()->json([
                'status' => 'no-zone-found',
                'message' => $message,
                'message_2' => $message_2,
                'message_3' => $message_3,
            ]);
        }

       }
       else{
           $message = "No Shipping Carrier Available For Your ".$country;
           $message_2 = "Delivery Time Not Available Right Now!";
           $message_3 = "Processing Time Not Found For This Product!";
           return response()->json([
               'status' => 'no-zone-found',
               'message' => $message,
               'message_2' => $message_2,
               'message_3' => $message_3,
           ]);
       }

    }

}
