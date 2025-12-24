<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Material;
use Livewire\Attributes\Validate;

class Materials extends Component
{
    public ?Material $editing_material = null;

    #[Validate('required|string|max:255')]
    public $name = '';

    #[Validate('required|string|max:255')]
    public $category = '';

    #[Validate('nullable|string|max:255')]
    public $sub_category = '';

    #[Validate('nullable|string|in:hb,hrc,hv')]
    public $hardness_unit = '';

    #[Validate('nullable|numeric')]
    public $hardness_value = '';

    #[Validate('nullable|string|max:1000')]
    public $notes = '';

    public function addMaterial()
    {
        $validated = $this->validate();

        if($validated['hardness_value'] === '') {
            $validated['hardness_value'] = null;
        }

        Material::create([
            'user_id' => auth()->id(),
            'name' => $validated['name'],
            'category' => $validated['category'],
            'sub_category' => $validated['sub_category'],
            'hardness_unit' => $validated['hardness_unit'],
            'hardness_value' => $validated['hardness_value'],
            'notes' => $validated['notes'],
        ]);

         $this->modal('add-material')->close();
    }

    public function editMaterial(Material $material)
    {
        $this->editing_material = $material;

        $this->name = $material->name;
        $this->category = $material->category;
        $this->sub_category = $material->sub_category;
        $this->hardness_unit = $material->hardness_unit;
        $this->hardness_value = $material->hardness_value;
        $this->notes = $material->notes;

        $this->modal('edit-material')->show();
    }

    public function updateMaterial()
    {
        $validated = $this->validate();

        if($validated['hardness_value'] === '') {
            $validated['hardness_value'] = null;
        }

        $this->editing_material->update([
            'name' => $validated['name'],
            'category' => $validated['category'],
            'sub_category' => $validated['sub_category'],
            'hardness_unit' => $validated['hardness_unit'],
            'hardness_value' => $validated['hardness_value'],
            'notes' => $validated['notes'],
        ]);

        $this->modal('edit-material')->close();
    }

    public function deleteMaterial(Material $material)
    {
        auth()->user()->materials()->where('id', $material->id)->delete();
    }

    public function render()
    {
        $materials = auth()->user()->materials()->get();
        return view('livewire.materials', compact('materials'));
    }
}
