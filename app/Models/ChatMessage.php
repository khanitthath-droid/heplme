<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    protected $fillable = [
        'chat_room_id',
        'sender_id',
        'message',
        'type',
        'image_path',
        'metadata',
        'is_read',
        'read_at',
        'deleted_by_sender',
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_read' => 'boolean',
        'deleted_by_sender' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function chatRoom()
    {
        return $this->belongsTo(ChatRoom::class, 'chat_room_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
