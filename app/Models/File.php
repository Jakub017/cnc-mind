<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'path',
        'type',
        'size',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function operation()
    {
        return $this->hasMany(Operation::class);
    }
}
