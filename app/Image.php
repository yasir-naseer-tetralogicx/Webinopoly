<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    public function has_variants(){
        return $this->hasMany('App\ProductVariant', 'image','id');
    }
}
