<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdminFileTemp extends Model
{
    protected $fillable = [
        'order_name','order_id','tracking_company','tracking_number','tracking_url','tracking_notes','file_id'
    ];
}
