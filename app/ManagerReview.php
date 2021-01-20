<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ManagerReview extends Model
{
    protected $fillable = [
        'name','email','review','rating','attachment','ticket_id','manager_id','user_id','shop_id'
    ];

    public function has_ticket(){
        return $this->belongsTo(Ticket::class,'ticket_id');
    }
}
