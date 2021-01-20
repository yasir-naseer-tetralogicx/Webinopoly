<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $fillable = [
        'user_id','wallet_token','available','pending','transferred','used'
    ];

    public function logs(){
        return $this->hasMany('App\WalletLog','wallet_id');
    }

    public function requests(){
        return $this->hasMany('App\WalletRequest','wallet_id');
    }

    public function owner(){
        return $this->belongsTo(User::class,'user_id');
    }



}
