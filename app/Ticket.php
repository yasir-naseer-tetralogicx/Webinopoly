<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    public function has_status()
    {
        return $this->belongsTo(TicketStatus::class,'status_id');
    }
    public function has_category()
    {
        return $this->belongsTo(TicketCategory::class,'category_id');
    }
    public function has_attachments()
    {
        return $this->hasMany(TicketAttachment::class,'ticket_id');
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
        return $this->hasMany(TicketThread::class,'ticket_id');
    }
    public function has_store()
    {
        return $this->belongsTo(Shop::class,'shop_id');
    }

    public function logs()
    {
        return $this->hasMany(TicketLog::class,'ticket_id');
    }
    public function has_reviews()
    {
        return $this->hasMany(ManagerReview::class,'ticket_id');
    }
}
