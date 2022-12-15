<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HGroup extends Model
{
    use HasFactory;

    protected $table = 'h_group';

    protected $fillable = [
        'message',
        'm_group_id',
        'user_id',
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
