<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WishlistThread extends Model
{
    public function has_manager()
    {
        return $this->belongsTo(User::class,'manager_id');
    }
    public function has_user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
    public function has_attachments()
    {
        return $this->hasMany(WishlistAttachment::class,'thread_id','id');
    }
}
