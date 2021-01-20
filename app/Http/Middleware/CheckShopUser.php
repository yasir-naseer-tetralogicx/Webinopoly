<?php

namespace App\Http\Middleware;

use App\Shop;
use Closure;
use Illuminate\Support\Facades\Auth;
use OhMyBrew\ShopifyApp\Facades\ShopifyApp;

class CheckShopUser
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
        /*Ossiset Shop Model*/
        $shop = ShopifyApp::shop();
        /*Local Shop Model!*/
        $shop = Shop::find($shop->id);
        if (count($shop->has_user) > 0) {
            return $next($request);
        } else {
            return redirect()->route('store.index');
        }
    }
}
