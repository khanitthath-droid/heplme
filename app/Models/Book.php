<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Book extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'author',
        'category',
        'description',
        'condition',
        'image',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}