<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VectorStore extends Model
{
    protected $fillable = ['user_id', 'google_id', 'name'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
