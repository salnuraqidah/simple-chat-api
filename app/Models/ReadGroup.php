<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReadGroup extends Model
{
    use HasFactory;

    protected $table = 'h_read_group';
    protected $fillable = [
        'user_id',
        'm_group_id',
        'last_read',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    public function group()
    {
        return $this->belongsTo(MGroup::class, 'user_id');
    }

}
