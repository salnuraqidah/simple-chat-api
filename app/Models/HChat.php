<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HChat extends Model
{
    use HasFactory;

    protected $table = 'h_chat';

    protected $fillable = [
        'message',
        'is_read',
        'm_chat_id',
        'user_to',
        'user_from'
    ];

    public function chat()
    {
        return $this->belongsTo(MChat::class, 'm_chat_id');
    }

    public function getTanggalAttribute($value)
    {
        if(empty($value)){
            return null;
        }else{
            return date('d-m-Y H:i:s', strtotime($value));
        }
    }

    
}
