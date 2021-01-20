<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RefundThread extends Model
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
        return $this->hasMany(RefundAttachment::class,'thread_id','id');
    }
}
