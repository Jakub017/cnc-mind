<?php

namespace App\Livewire;

use Livewire\Component;
use Gemini\Data\GenerationConfig;
use Gemini\Data\Schema;
use Gemini\Enums\DataType;
use Gemini\Enums\ResponseMimeType;
use Gemini\Laravel\Facades\Gemini;
use App\Models\Tool;
use App\Models\Material;
use Livewire\Attributes\Validate;
use App\Models\Operation;
use Masmerise\Toaster\Toaster;

class Operations extends Component
{
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
    public $feed_rate_vf = null;
    public $depth_of_cut_ap = null;
    public $width_of_cut_ae = null;
    public $notes = '';

    public function addOperation()
    {
        $validated = $this->validate();

        $tool = Tool::find($validated['tool_id']);
        $material = Material::find($validated['material_id']);

        $prompt = "Jesteś ekspertem technologii CNC z 20-letnim doświadczeniem. Twoim priorytetem jest trwałość narzędzia i stabilność procesu. żOblicz BEZPIECZNE i REALNE parametry skrawania.
        DANE OPERACJI:
        - Opis/cel: {$validated['description']}

        NARZĘDZIE:
        - Typ: {$tool->typePromptLabel()}
        - Materiał narzędzia: {$tool->materialPromptLabel()}\n";

        if($tool->diameter) {
            $prompt .= "- Średnica narzędzia: {$tool->diameter} mm \n";
        };
        if($tool->flutes) {
            $prompt .= "- Liczba ostrzy narzędzia (z): {$tool->flutes} \n";
        };
        if($tool->insert_shape) {
            $prompt .= "- Kształt płytki narzędzia: {$tool->insertShapePromptLabel()} \n";
        };
        if($tool->insert_code) {
            $prompt .= "- Kod płytki: {$tool->insert_code} \n";
        };

        $prompt .= "\n\n";

        $prompt .= "MATERIAŁ OBRABIANY:\n";
        $prompt .= "- Kategoria: {$material->categoryPromptLabel()}\n";
        if($material->sub_category) {
            $prompt .= "- Podkategoria: {$material->subCategoryPromptLabel()}\n";
        };
        if($material->hardness_value) {
            $prompt .= "- Twardość: {$material->hardness_value} {$material->hardness_unit}\n";
        };
        if($material->notes) {
            $prompt .= "- Dodatkowe uwagi o materiale: {$material->notes}\n";
        };

        // dd($prompt); 

        $result = Gemini::generativeModel(model: 'gemini-2.5-flash-lite')
            ->withGenerationConfig(
                generationConfig: new GenerationConfig(
                    responseMimeType: ResponseMimeType::APPLICATION_JSON,
                    responseSchema: new Schema(
                        type: DataType::ARRAY,
                        items: new Schema(
                            type: DataType::OBJECT,
                            properties: [
                                'cutting_speed_vc' => new Schema(type: DataType::NUMBER),
                                'spindle_speed_n' => new Schema(type: DataType::INTEGER),
                                'feed_per_tooth_fz' => new Schema(type: DataType::NUMBER),
                                'feed_rate_vf' => new Schema(type: DataType::INTEGER),
                                'depth_of_cut_ap' => new Schema(type: DataType::NUMBER),
                                'width_of_cut_ae' => new Schema(type: DataType::NUMBER),
                                'notes' => new Schema(type: DataType::STRING),

                            ],
                            required: ['cutting_speed_vc', 'spindle_speed_n', 'feed_per_tooth_fz', 'feed_rate_vf', 'depth_of_cut_ap', 'width_of_cut_ae', 'notes'],
                        )
                    )
        )
    )
    ->generateContent($prompt);
        $attributes = $result->json()[0];
        $this->cutting_speed_vc = $attributes->cutting_speed_vc;
        $this->spindle_speed_n = $attributes->spindle_speed_n;
        $this->feed_per_tooth_fz = $attributes->feed_per_tooth_fz;
        $this->feed_rate_vf = $attributes->feed_rate_vf;
        $this->depth_of_cut_ap = $attributes->depth_of_cut_ap;
        $this->width_of_cut_ae = $attributes->width_of_cut_ae;
        $this->notes = $attributes->notes;

        Operation::create([
            'user_id' => auth()->user()->id,
            'name' => $this->name,
            'description' => $this->description,
            'tool_id' => $this->tool_id,
            'material_id' => $this->material_id,
            'cutting_speed_vc' => $this->cutting_speed_vc,
            'spindle_speed_n' => $this->spindle_speed_n,
            'feed_per_tooth_fz' => $this->feed_per_tooth_fz,
            'feed_rate_vf' => $this->feed_rate_vf,
            'depth_of_cut_ap' => $this->depth_of_cut_ap,
            'width_of_cut_ae' => $this->width_of_cut_ae,
            'notes' => $this->notes,
        ]);
        $this->visibleAnswer = true;
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
        $this->feed_rate_vf = $operation->feed_rate_vf;
        $this->depth_of_cut_ap = $operation->depth_of_cut_ap;
        $this->width_of_cut_ae = $operation->width_of_cut_ae;
        $this->notes = $operation->notes;

        $this->modal('see-operation')->show();
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
