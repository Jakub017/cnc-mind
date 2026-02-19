<?php

namespace App\Livewire;

use App\Ai\Agents\CodeSpecialist;
use App\Ai\Agents\ParametersSpecialist;
use App\Models\Material;
use App\Models\Operation;
use App\Models\Tool;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;
use Masmerise\Toaster\Toaster;

class Operations extends Component
{
    use WithPagination;
    
    public $visible_answer = false;

    public $current_operation;

    #[Validate('required|string|max:255')]
    public $name = '';

    #[Validate('string|max:1000')]
    public $description = '';

    #[Validate('required')]
    public $tool_id = '';

    #[Validate('required')]
    public $material_id = '';

    public $cutting_speed_vc = null;

    public $spindle_speed_n = null;

    public $feed_per_tooth_fz = null;

    public $feed_per_revolution_fn = null;

    public $feed_rate_vf = null;

    public $depth_of_cut_ap = null;

    public $width_of_cut_ae = null;

    public $theoretical_roughness_ra = null;

    public $g_code = '';

    public $want_g_code = false;

    public $notes = '';


    public function showAddOperationModal()
    {
        $this->reset();
        $this->modal('add-operation')->show();
    }

    public function addOperation()
    {
        $validated = $this->validate();

        $tool = Tool::find($validated['tool_id']);
        $material = Material::find($validated['material_id']);

        $parametersAgent = new ParametersSpecialist(
            tool: $tool,
            material: $material,
            description: $validated['description'] ?? '',
        );

        $parametersResponse = $parametersAgent->prompt('Przeanalizuj te dane i oblicz optymalne parametry skrawania dla tej operacji.');

        $this->cutting_speed_vc = round($parametersResponse['cutting_speed_vc'], 2);
        $this->spindle_speed_n = round(($this->cutting_speed_vc * 1000) / (M_PI * $parametersResponse['effective_diameter']), 0);
        $this->feed_per_tooth_fz = isset($parametersResponse['feed_per_tooth_fz']) ? round($parametersResponse['feed_per_tooth_fz'], 4) : null;
        $this->feed_per_revolution_fn = isset($parametersResponse['feed_per_revolution_fn']) ? round($parametersResponse['feed_per_revolution_fn'], 4) : null;
        if ($tool->type == 'turning_tool') {
            $this->feed_rate_vf = round(($this->feed_per_revolution_fn ?? 0) * $this->spindle_speed_n, 0);
        } else {
            $this->feed_rate_vf = round(($this->feed_per_tooth_fz ?? 0) * ($tool->flutes ?? 1) * $this->spindle_speed_n, 0);
        }
        $this->depth_of_cut_ap = round($parametersResponse['depth_of_cut_ap'], 2);
        $this->width_of_cut_ae = isset($parametersResponse['width_of_cut_ae']) ? round($parametersResponse['width_of_cut_ae'], 2) : null;
        $this->theoretical_roughness_ra = isset($parametersResponse['theoretical_roughness_ra']) ? round($parametersResponse['theoretical_roughness_ra'], 2) : null;
        $this->notes = $parametersResponse['notes'];

        if ($this->want_g_code) {
            $codeAgent = new CodeSpecialist(
                spindle_speed_n: $this->spindle_speed_n,
                description: $validated['description'] ?? '',
                tool: $tool,
                feed_per_revolution_fn: $this->feed_per_revolution_fn,
                feed_rate_vf: $this->feed_rate_vf,
                width_of_cut_ae: $this->width_of_cut_ae,
                depth_of_cut_ap: $this->depth_of_cut_ap,
            );

            $codeResponse = $codeAgent->prompt('Przygotuj G-Code dla tej operacji.');

            $this->g_code = str_replace(['```gcode', '```'], '', $codeResponse);
        }

        $this->visible_answer = true;

        Operation::create([
            'user_id' => auth()->user()->id,
            'name' => $this->name,
            'description' => $this->description,
            'tool_id' => $this->tool_id,
            'material_id' => $this->material_id,
            'cutting_speed_vc' => $this->cutting_speed_vc,
            'spindle_speed_n' => $this->spindle_speed_n,
            'feed_per_tooth_fz' => $this->feed_per_tooth_fz,
            'feed_per_revolution_fn' => $this->feed_per_revolution_fn,
            'feed_rate_vf' => $this->feed_rate_vf,
            'depth_of_cut_ap' => $this->depth_of_cut_ap,
            'width_of_cut_ae' => $this->width_of_cut_ae,
            'theoretical_roughness_ra' => $this->theoretical_roughness_ra,
            'g_code' => $this->g_code,
            'notes' => $this->notes,
        ]);

        Toaster::success(__('Operation has been successfully added.'));
    }

    public function seeOperation(Operation $operation)
    {
        $this->current_operation = $operation;

        $this->name = $operation->name;
        $this->description = $operation->description;
        $this->tool_id = $operation->tool_id;
        $this->material_id = $operation->material_id;
        $this->cutting_speed_vc = $operation->cutting_speed_vc;
        $this->spindle_speed_n = $operation->spindle_speed_n;
        $this->feed_per_tooth_fz = $operation->feed_per_tooth_fz;
        $this->feed_per_revolution_fn = $operation->feed_per_revolution_fn;
        $this->feed_rate_vf = $operation->feed_rate_vf;
        $this->depth_of_cut_ap = $operation->depth_of_cut_ap;
        $this->width_of_cut_ae = $operation->width_of_cut_ae;
        $this->theoretical_roughness_ra = $operation->theoretical_roughness_ra;
        $this->g_code = $operation->g_code;
        $this->notes = $operation->notes;

        $this->modal('see-operation')->show();
    }

    public function editOperation(Operation $operation)
    {
        $this->current_operation = $operation;

        $this->name = $operation->name;
        $this->description = $operation->description;
        $this->tool_id = $operation->tool_id;
        $this->material_id = $operation->material_id;
        $this->cutting_speed_vc = $operation->cutting_speed_vc;
        $this->spindle_speed_n = $operation->spindle_speed_n;
        $this->feed_per_tooth_fz = $operation->feed_per_tooth_fz;
        $this->feed_per_revolution_fn = $operation->feed_per_revolution_fn;
        $this->feed_rate_vf = $operation->feed_rate_vf;
        $this->depth_of_cut_ap = $operation->depth_of_cut_ap;
        $this->width_of_cut_ae = $operation->width_of_cut_ae;
        $this->theoretical_roughness_ra = $operation->theoretical_roughness_ra;
        $this->g_code = $operation->g_code;
        $this->notes = $operation->notes;

        $this->modal('edit-operation')->show();
    }

    public function updateOperation()
    {

        $this->current_operation->update([
            'name' => $this->name,
            'description' => $this->description,
            'tool_id' => $this->tool_id,
            'material_id' => $this->material_id,
            'cutting_speed_vc' => $this->cutting_speed_vc,
            'spindle_speed_n' => $this->spindle_speed_n,
            'feed_per_tooth_fz' => $this->feed_per_tooth_fz,
            'feed_per_revolution_fn' => $this->feed_per_revolution_fn,
            'feed_rate_vf' => $this->feed_rate_vf,
            'depth_of_cut_ap' => $this->depth_of_cut_ap,
            'width_of_cut_ae' => $this->width_of_cut_ae,
            'theoretical_roughness_ra' => $this->theoretical_roughness_ra,
            'g_code' => $this->g_code ?? '',
            'notes' => $this->notes,
        ]);
        $this->modal('edit-operation')->close();
        Toaster::success(__('Operation has been successfully updated.'));
    }

    public function deleteOperation(Operation $operation)
    {
        auth()->user()->operations()->where('id', $operation->id)->delete();
        Toaster::success(__('Operation has been successfully deleted.'));
    }

    public function render()
    {
        $operations = auth()->user()->operations()->paginate(5);
        $tools = auth()->user()->tools()->get();
        $materials = auth()->user()->materials()->get();

        return view('livewire.operations', compact('operations', 'tools', 'materials'));
    }
}
