<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'role',
        'phone',
        'line_id',
        'exchange_area',
        'last_seen_at',
    ];  

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_seen_at' => 'datetime',
        ];
    }

    public function isOnline(): bool
    {
        return $this->last_seen_at && $this->last_seen_at->gt(now()->subMinutes(3));
    }

    public function books()
    {
        return $this->hasMany(Book::class);
    }

    public function wantedBooks()
    {
        return $this->hasMany(WantedBook::class);
    }

    public function blockedUsers()
    {
        return $this->hasMany(UserBlock::class, 'user_id');
    }

    public function isBlockedWith($otherUserId): bool
    {
        return UserBlock::where(function ($q) use ($otherUserId) {
            $q->where('user_id', $this->id)->where('blocked_user_id', $otherUserId);
        })->orWhere(function ($q) use ($otherUserId) {
            $q->where('user_id', $otherUserId)->where('blocked_user_id', $this->id);
        })->exists();
    }
}

