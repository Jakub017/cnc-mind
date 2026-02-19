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

    public function operations()
    {
        return $this->hasMany(Operation::class);
    }

    // Prompt labels (for better understanding)

    public function categoryPromptLabel()
    {
        return match ($this->category) {
            'p' => 'P (Stale)',
            'm' => 'M (Stale nierdzewne)',
            'k' => 'K (Żeliwa)',
            'n' => 'N (Metale nieżelazne / Aluminium)',
            's' => 'S (Superstopy / Tytan)',
            'h' => 'H (Stale hartowane)',
            default => $this->category,
        };
    }

    public function subCategoryPromptLabel()
    {
        return match ($this->sub_category) {
            'p1' => 'P1 (Niskowęglowa)',
            'p2' => 'P2 (Średniowęglowa)',
            'p3' => 'P3 (Stal stopowa - niska)',
            'p4' => 'P4 (Stal stopowa - wysoka)',
            'p5' => 'P5 (Stal narzędziowa)',
            'm1' => 'M1 (Austenityczna)',
            'm2' => 'M2 (Ferrytyczna / Martenityczna)',
            'm3' => 'M3 (Duplex)',
            'n1' => 'N1 (Aluminium < 12% Si)',
            'n2' => 'N2 (Aluminium > 12% Si)',
            'n3' => 'N3 (Stopy miedzi)',
            's1' => 'S1 (Na bazie żelaza)',
            's2' => 'S2 (Na bazie niklu / kobaltu)',
            's3' => 'S3 (Stopy tytanu)',
            'h1' => 'H1 (Hartowane < 55 HRC)',
            'h2' => 'H2 (Hartowane > 55 HRC)',
            default => $this->sub_category,
        };
    }
}
