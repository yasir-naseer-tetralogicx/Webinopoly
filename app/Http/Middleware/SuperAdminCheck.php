<?php

namespace App\Http\Middleware;

use Closure;
use OhMyBrew\ShopifyApp\Facades\ShopifyApp;

class SuperAdminCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $shop = ShopifyApp::shop();
//        dd( in_array($shop->shopify_domain,['fantasy-supplier.myshopify.com','wefullfill.myshopify.com']));
        if(in_array($shop->shopify_domain,['wefullfill.myshopify.com'])){
            return $next($request);
        }
        else{
            return redirect('login');
        }
    }
}
