<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
        'reporter_id',
        'reported_user_id',
        'chat_room_id',
        'chat_message_id',
        'reason',
        'details',
        'status',
        'admin_notes',
    ];

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function reportedUser()
    {
        return $this->belongsTo(User::class, 'reported_user_id');
    }

    public function chatRoom()
    {
        return $this->belongsTo(ChatRoom::class, 'chat_room_id');
    }

    public function chatMessage()
    {
        return $this->belongsTo(ChatMessage::class, 'chat_message_id');
    }
}
