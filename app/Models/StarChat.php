<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StarChat extends Model
{
    use HasFactory;

    protected $table = 'm_star_chat';

    protected $fillable = [
        'user_id',
        'h_group_id',
        'h_chat_id',
    ];

    public function personal()
    {
        return $this->belongsTo(HChat::class, 'h_chat_id');
    }

    public function group()
    {
        return $this->belongsTo(HGroup::class, 'h_group_id');
    }
}
