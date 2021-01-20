<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    protected $fillable = ['name', 'status', 'time'];

    public function users() {
        return $this->belongsToMany(User::class, 'campaigns_users')->withPivot('status');
    }
}
