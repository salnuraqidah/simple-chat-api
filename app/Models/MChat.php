<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MChat extends Model
{
    use HasFactory;
    protected $table = 'm_chat';

    protected $fillable = [
        'user1',
        'user2',
    ];

    public function user1()
    {
        return $this->belongsTo(User::class, 'user1');
    }

    public function user2()
    {
        return $this->belongsTo(User::class, 'user2');
    }

    public function chats()
    {
        return $this->HasMany(HChat::class, 'm_chat_id');
    }
}
