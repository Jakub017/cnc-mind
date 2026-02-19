<?php

namespace App\Livewire;

use App\Models\Tool;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;
use Masmerise\Toaster\Toaster;

class Tools extends Component
{
    use WithPagination;

    public ?Tool $editing_tool = null;

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

    public function showAddToolModal()
    {
        $this->reset();
        $this->modal('add-tool')->show();
    }

    public function addTool()
    {
        $validated = $this->validate();

        if ($validated['diameter'] === '') {
            $validated['diameter'] = null;
        }

        if ($validated['flutes'] === '') {
            $validated['flutes'] = 1;
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
        Toaster::success(__('Tool has been successfully added.'));
    }

    public function editTool(Tool $tool)
    {
        $this->editing_tool = $tool;

        $this->name = $tool->name;
        $this->type = $tool->type;
        $this->material = $tool->material;
        $this->diameter = $tool->diameter;
        $this->flutes = $tool->flutes;
        $this->insert_shape = $tool->insert_shape;
        $this->insert_code = $tool->insert_code;

        $this->modal('edit-tool')->show();
    }

    public function updateTool()
    {
        $validated = $this->validate();

        if ($validated['diameter'] === '') {
            $validated['diameter'] = null;
        }

        if ($validated['flutes'] === '') {
            $validated['flutes'] = null;
        }

        $this->editing_tool->update([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'material' => $validated['material'],
            'diameter' => $validated['diameter'],
            'flutes' => $validated['flutes'],
            'insert_shape' => $validated['insert_shape'],
            'insert_code' => $validated['insert_code'],
        ]);

        $this->modal('edit-tool')->close();
        Toaster::success(__('Tool has been successfully updated.'));
    }

    public function deleteTool(Tool $tool)
    {
        auth()->user()->tools()->where('id', $tool->id)->delete();
        Toaster::success(__('Tool has been successfully deleted.'));
    }

    public function render()
    {
        $tools = auth()->user()->tools()->paginate(5);

        return view('livewire.tools', compact('tools'));
    }
}
