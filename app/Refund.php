<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{
    public function has_order(){
        return $this->belongsTo(RetailerOrder::class,'order_id');
    }
    public function has_status()
    {
        return $this->belongsTo(TicketStatus::class,'status_id');
    }
    public function has_attachments()
    {
        return $this->hasMany(RefundAttachment::class,'refund_id');
    }
    public function has_user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
    public function has_manager()
    {
        return $this->belongsTo(User::class,'manager_id');
    }
    public function has_thread()
    {
        return $this->hasMany(RefundThread::class,'refund_id');
    }
    public function has_store()
    {
        return $this->belongsTo(Shop::class,'shop_id');
    }

    public function logs()
    {
        return $this->hasMany(RefundLog::class,'refund_id');
    }
}
