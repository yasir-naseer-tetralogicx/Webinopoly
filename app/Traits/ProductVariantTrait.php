<?php

namespace App\Traits;

trait ProductVariantTrait {

    public function option1($product){
        if(count($product->hasVariants) > 0){

            $array = array_unique($product->hasVariants->pluck('option1')->toArray());
            if($array[0] == ""){
                $array =[];
            }
            return $array;
        }
        else{
            return [];
        }

    }
    public function option2($product){
        if(count($product->hasVariants) > 0){

            $array =  array_unique($product->hasVariants->pluck('option2')->toArray());
            if($array[0] == ""){
                $array =[];
            }
            return $array;
        }
        else{
            return [];
        }
    }
    public function option3($product){
        if(count($product->hasVariants) > 0){

            $array =  array_unique($product->hasVariants->pluck('option3')->toArray());
            if($array[0] == ""){
                $array =[];
            }
            return $array;
        }
        else{
            return [];
        }
    }
    public function category($product){
        if(count($product->has_categories) > 0){
            $array =  array_unique($product->has_categories->pluck('id')->toArray());
            if($array[0] == ""){
                $array =[];
            }
            return $array;
        }
        else{
            return [];
        }
    }
    public function subcategory($product){
        if(count($product->has_subcategories) > 0) {
            $array = array_unique($product->has_subcategories->pluck('id')->toArray());
            if ($array[0] == "") {
                $array = [];
            }

            return $array;
        }
        else{
                return [];
            }
    }
    public function varaint_count($product){
        $sum =  $product->hasVariants->sum('quantity');
        return $sum;
    }
    public function warned_platforms($product){
        $array =  $product->has_platforms->pluck('id')->toArray();
        return $array;
    }
    public function images($product){
        $array =  $product->hasVariants->pluck('image')->toArray();
        $temp = [];
        foreach ($array as $a){
            if($a != null){
                array_push($temp,$a);
            }
        }
        return $temp;
    }


}
