<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RetailerImage extends Model
{
    public function has_variants(){
        return $this->hasMany('App\RetailerProductVariant', 'image','id');
    }
}
