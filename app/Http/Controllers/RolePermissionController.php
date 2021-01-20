<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RolePermissionController extends Controller
{
   public function check_roles(){
       $user = Auth::user();
//       if($user->email == 'admin@wefullfill.com'){
//           return redirect('/shop/install?shop=fantasy-supplier.myshopify.com');
//       }

       if($user->hasRole('wordpress-admin')) {
           return redirect('/');
       }
       if ($user->email == 'super_admin@wefullfill.com'){
           return redirect('/shop/install?shop=wefullfill.myshopify.com');
       }
       else{
           if($user->hasRole('non-shopify-users') && $user->hasRole('sales-manager')){
               return redirect()->route('system.selection');
           }
           else{
               if($user->hasRole('non-shopify-users')){
                   return redirect()->route('users.dashboard',['ftl' => '1']);
               }
               else if($user->hasRole('sales-manager')){
                   return redirect()->route('managers.dashboard');
               }
               else{
                   return redirect()->route('admin.dashboard');
               }
           }
       }

   }
   public function selection(){
       return view('selection');
   }

   public function store_connect(Request $request){
       return view('non_shopify_users.store_connect');
   }
}
