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

    // Prompt labels (for better understanding)

    public function typePromptLabel()
    {
        return match($this->type) {
            'end_mill' => "Frez",
            'turning_tool' => "Nóż tokarski",
            'drill' => "Wiertło",
            'face_mill' => "Głowica frezarska",
            'center_drill' => "Nawiertak",
            default => $this->type,
        };
    }

    public function materialPromptLabel()
    {
        return match($this->material) {
            'solid_carbide' => "Węglik spiekany",
            'hss' => "Stal szybkotnąca",
            'carbide_insert' => "Węglik (Płytka wymienna)",
            'pcd' => "PCD (Diament)",
            'ceramic' => "Ceramika",
            default => $this->material,
        };
    }

    public function insertShapePromptLabel()
    {
        return match($this->insert_shape) {
            'c' => 'C (Romb 80°)',
            'd' => 'D (Romb 55°)',
            't' => 'T (Trójkąt)',
            'w' => 'W (Trigon)',
            's' => 'S (Kwadrat)',
            'v' => 'V (Romb 35°)',
            'r' => 'R (Romb 60°)',
            default => $this->insert_shape,
        };
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function operations()
    {
        return $this->hasMany(Operation::class);
    }
}
