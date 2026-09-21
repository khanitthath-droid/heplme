<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Book;

class TradeRing extends Model
{
    protected $fillable = [
        'initiator_id',
        'user1_id',
        'book1_id',
        'user1_status',
        'user2_id',
        'book2_id',
        'user2_status',
        'user3_id',
        'book3_id',
        'user3_status',
        'status',
    ];

    public function initiator()
    {
        return $this->belongsTo(User::class, 'initiator_id');
    }

    public function user1()
    {
        return $this->belongsTo(User::class, 'user1_id');
    }

    public function book1()
    {
        return $this->belongsTo(Book::class, 'book1_id');
    }

    public function user2()
    {
        return $this->belongsTo(User::class, 'user2_id');
    }

    public function book2()
    {
        return $this->belongsTo(Book::class, 'book2_id');
    }

    public function user3()
    {
        return $this->belongsTo(User::class, 'user3_id');
    }

    public function book3()
    {
        return $this->belongsTo(Book::class, 'book3_id');
    }

    /**
     * ตรวจสอบว่า User ID ที่ระบุเป็นหนึ่งใน 3 ฝ่ายหรือไม่
     */
    public function isParticipant(int $userId): bool
    {
        return in_array($userId, [$this->user1_id, $this->user2_id, $this->user3_id]);
    }

    /**
     * ดึงสถานะการยืนยันของ User ที่ระบุ
     */
    public function getUserStatus(int $userId): ?string
    {
        if ($this->user1_id == $userId) return $this->user1_status;
        if ($this->user2_id == $userId) return $this->user2_status;
        if ($this->user3_id == $userId) return $this->user3_status;
        return null;
    }

    /**
     * อัปเดตสถานะของ User ที่ระบุ
     */
    public function setUserStatus(int $userId, string $status): void
    {
        if ($this->user1_id == $userId) $this->user1_status = $status;
        if ($this->user2_id == $userId) $this->user2_status = $status;
        if ($this->user3_id == $userId) $this->user3_status = $status;
    }

    /**
     * ตรวจสอบว่าทั้ง 3 ฝ่ายกดยืนยัน (accepted) ครบทุกคนแล้วหรือไม่
     */
    public function allAccepted(): bool
    {
        return $this->user1_status === 'accepted' &&
               $this->user2_status === 'accepted' &&
               $this->user3_status === 'accepted';
    }

    /**
     * นับจำนวนฝ่ายที่กดยืนยันแล้ว (accepted count)
     */
    public function acceptedCount(): int
    {
        $count = 0;
        if ($this->user1_status === 'accepted') $count++;
        if ($this->user2_status === 'accepted') $count++;
        if ($this->user3_status === 'accepted') $count++;
        return $count;
    }
}
