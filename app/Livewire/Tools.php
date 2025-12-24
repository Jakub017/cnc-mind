<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Validate;
use App\Models\Tool;

class Tools extends Component
{
    #[Validate('required|string|max:255')]
    public $name = '';

    #[Validate('required|string|max:255')]
    public $type = '';

    #[Validate('required|string|max:255')]
    public $material = '';

    #[Validate('nullable|numeric')]
    public $diameter = '';

    #[Validate('nullable|numeric')]
    public $flutes = '';

    #[Validate('nullable|string|max:255')]
    public $insert_shape = '';

    #[Validate('nullable|string|max:255')]
    public $insert_code = '';


    public function addTool()
    {
        $validated = $this->validate();

        if($validated['diameter'] === '') {
            $validated['diameter'] = null;
        }

        if($validated['flutes'] === '') {
            $validated['flutes'] = null;
        }

        Tool::create([
            'user_id' => auth()->id(),
            'name' => $validated['name'],
            'type' => $validated['type'],
            'material' => $validated['material'],
            'diameter' => $validated['diameter'],
            'flutes' => $validated['flutes'],
            'insert_shape' => $validated['insert_shape'],
            'insert_code' => $validated['insert_code'],
        ]);

        $this->modal('add-tool')->close();
    }

    public function deleteTool(Tool $tool)
    {
        auth()->user()->tools()->where('id', $tool->id)->delete();
    }

    public function render()
    {
        $tools = auth()->user()->tools()->get();
        return view('livewire.tools', compact('tools'));
    }
}
