<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ChatRoom extends Model
{
    protected $fillable = [
        'exchange_request_id',
        'user1_id',
        'user2_id',
        'last_message_at',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    public function exchangeRequest()
    {
        return $this->belongsTo(ExchangeRequest::class, 'exchange_request_id');
    }

    public function user1()
    {
        return $this->belongsTo(User::class, 'user1_id');
    }

    public function user2()
    {
        return $this->belongsTo(User::class, 'user2_id');
    }

    public function messages()
    {
        return $this->hasMany(ChatMessage::class, 'chat_room_id');
    }

    public function latestMessage()
    {
        return $this->hasOne(ChatMessage::class, 'chat_room_id')->latestOfMany();
    }

    public function otherUser($userId = null)
    {
        $currentId = $userId ?? Auth::id();
        return $this->user1_id == $currentId ? $this->user2 : $this->user1;
    }

    public function unreadCountFor($userId = null)
    {
        $currentId = $userId ?? Auth::id();
        return $this->messages()
            ->where('sender_id', '!=', $currentId)
            ->where('is_read', false)
            ->count();
    }
}
