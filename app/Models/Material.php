<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'category',
        'sub_category',
        'hardness_value',
        'hardness_unit',
        'notes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
