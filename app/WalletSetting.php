<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WalletSetting extends Model
{
    protected $fillable = ['user_id', 'enable'];
}
