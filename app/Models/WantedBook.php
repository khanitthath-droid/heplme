<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class WantedBook extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'author',
        'description',
        'image',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}