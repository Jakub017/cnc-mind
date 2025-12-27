<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Operation extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'tool_id',
        'material_id',
        'cutting_speed_vc',
        'spindle_speed_n',
        'feed_per_tooth_fz',
        'feed_rate_vf',
        'depth_of_cut_ap',
        'width_of_cut_ae',
        'g_code',
        'notes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tool()
    {
        return $this->belongsTo(Tool::class);
    }

    public function material()
    {
         return $this->belongsTo(Material::class);
    }
}
