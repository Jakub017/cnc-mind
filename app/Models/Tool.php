<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tool extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'type',
        'material',
        'diameter',
        'flutes',
        'insert_shape',
        'insert_code',
    ];

    public function typeLabel()
    {
        return match($this->type) {
            'end_mill' => __('End mill'),
            'turning_tool' => __('Turning tool'),
            'drill' => __('Drill'),
            'face_mill' => __('Face mill'),
            'center_drill' => __('Center drill'),
            default => $this->type,
        };
    }

    public function materialLabel()
    {
        return match($this->material) {
            'solid_carbide' => __('Solid Carbide'),
            'hss' => __('HSS'),
            'carbide_insert' => __('Carbide (Insert)'),
            'pcd' => __('PCD (Diamond)'),
            'ceramic' => __('Ceramic'),
            default => $this->material,
        };
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
