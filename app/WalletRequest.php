<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WalletRequest extends Model
{
   protected $fillable = [
       'user_id', 'wallet_id','amount','bank_name','cheque','cheque_title','notes','status','type'
   ];

    public function getUserAttribute() {
        $user = User::find($this->user_id);
        return $user->name;
    }

    public function getTokenAttribute() {
        $wallet = Wallet::find($this->wallet_id);
        return $wallet->wallet_token;
    }
}
