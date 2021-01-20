<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    protected $fillable = ['title', 'category_id'];
    public function hasCategory(){
        return $this->belongsTo('App\Category', 'category_id', 'id');
    }
}
