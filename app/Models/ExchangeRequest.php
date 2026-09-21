<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Book;

class ExchangeRequest extends Model
{
    protected $fillable = [
        'requester_id',
        'receiver_id',
        'offered_book_id',
        'requested_book_id',
        'status',
        'requester_confirmed_at',
        'receiver_confirmed_at',
    ];

    protected $casts = [
        'requester_confirmed_at' => 'datetime',
        'receiver_confirmed_at' => 'datetime',
    ];

    public function chatRoom()
    {
        return $this->hasOne(ChatRoom::class, 'exchange_request_id');
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function offeredBook()
    {
        return $this->belongsTo(Book::class, 'offered_book_id');
    }

    public function requestedBook()
    {
        return $this->belongsTo(Book::class, 'requested_book_id');
    }
}