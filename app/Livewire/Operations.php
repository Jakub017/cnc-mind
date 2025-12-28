<?php

namespace App\Livewire;

use Livewire\Component;
use OpenAI\Laravel\Facades\OpenAI;
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
    public $g_code = '';
    public $want_g_code = false;
    public $notes = '';

    public function addOperation()
    {
        set_time_limit(120);
        $validated = $this->validate();

        $tool = Tool::find($validated['tool_id']);
        $material = Material::find($validated['material_id']);

        $prompt = "
        Jesteś ekspertem technologii CNC. Twoim zadaniem jest obliczenie parametrów skrawania i wygenerowanie G-kodu.
        
        DANE WEJŚCIOWE:
        OPIS/CEL OPERACJI: {$validated['description']}
        NARZĘDZIE:
        - Typ: {$tool->typePromptLabel()}
        - Materiał: {$tool->materialPromptLabel()}
        - Średnica (D): " . ($tool->diameter ?? 'nieznana') . " mm
        - Liczba ostrzy (z): " . ($tool->flutes ?? '1') . "
        " . ($tool->insert_shape ? "- Kształt płytki: {$tool->insertShapePromptLabel()}\n" : "") . "
        " . ($tool->insert_code ? "- Kod płytki: {$tool->insert_code}\n" : "") . "

        MATERIAŁ OBRABIANY:
        - Kategoria: {$material->categoryPromptLabel()}
        - Podkategoria: {$material->subCategoryPromptLabel()}
        - Twardość: {$material->hardness_value} {$material->hardness_unit}
        " . ($material->notes ? "- Uwagi: {$material->notes}\n" : "") . "

        ZASADY OBLICZEŃ (BEZWZGLĘDNE):
        1. Oblicz obroty n: n = (vc * 1000) / (π * D).
        2. Oblicz posuw minutowy vf: vf = n * fz * z.
        3. Wszystkie wyniki muszą być spójne matematycznie. Jeśli vc=30 i D=10, n MUSI wynosić ok. 955.
        4. Dostosuj vc i fz do materiału: dla stali nierdzewnej i VHM celuj w bezpieczne, ale wydajne parametry.

        ZASADY G-CODE (ISO Standard):
        1. Zawsze dodaj S (obroty) i M3 (start wrzeciona).
        2. Zawsze używaj G43 H (kompensacja długości).
        3. Nigdy nie wjeżdżaj w materiał szybkim ruchem G0 Z-. Użyj bezpiecznego dojazdu G1 z posuwem (wejście rampą lub powolne wejście w osi Z).
        4. Jeśli w opisie jest 'chłodzenie', dodaj M8.
        5. Zakończ program bezpiecznym odjazdem Z i M30.
        ";

        if($this->want_g_code) {
            $prompt .= "\nWygeneruj kompletny, bezpieczny i zgodny z wygenerowanymi przez ciebie parametrami G-code dla powyższej operacji.";
        }

        $properties = [
        'cutting_speed_vc' => ['type' => 'number'],
        'feed_per_tooth_fz' => ['type' => 'number'],
        'depth_of_cut_ap' => ['type' => 'number'],
        'width_of_cut_ae' => ['type' => 'number'],
        'notes' => ['type' => 'string'],
        ];

        $required = [
            'cutting_speed_vc', 'feed_per_tooth_fz', 'depth_of_cut_ap', 'width_of_cut_ae', 'notes'
        ];

        if ($this->want_g_code) {
            $properties['g_code'] = ['type' => 'string'];
            $required[] = 'g_code';
        }

        $response = OpenAI::chat()->create([
            'model' => 'gpt-4o',
            'messages' => [
                ['role' => 'system', 
                'content' => "Jesteś precyzyjnym kalkulatorem CNC i programistą CAM. 
                Twoim priorytetem jest: 
                1. Spójność matematyczna. 
                2. Bezpieczeństwo narzędzia (brak kolizji, odpowiednie wejście w materiał). 
                3. Krótka (maksymalnie 2 zdania) techniczna notatka w języku " . app()->getLocale() . ".
                4. W notatce zapisz tylko ogólne podsumowanie, nie pisz żadnych liczb."],
                ['role' => 'user', 'content' => $prompt]
            ],
            'response_format' => [
                'type' => 'json_schema',
                'json_schema' => [
                    'name' => 'cnc_parameters',
                    'strict' => true,
                    'schema' => [
                        'type' => 'object',
                        'properties' => $properties,
                        'required' => $required,
                        'additionalProperties' => false
                    ]
                ],
            ],
        ]);

        $jsonString = $response['choices'][0]['message']['content'];

        $attributes = json_decode($jsonString, true);

        $this->cutting_speed_vc = $attributes['cutting_speed_vc'];
        $this->spindle_speed_n = round(($attributes['cutting_speed_vc'] * 1000) / (M_PI * $tool->diameter), 0);
        $this->feed_per_tooth_fz = $attributes['feed_per_tooth_fz'];
        $this->feed_rate_vf = round($attributes['feed_per_tooth_fz'] * $tool->flutes * $this->spindle_speed_n, 0);
        $this->depth_of_cut_ap = $attributes['depth_of_cut_ap'];
        $this->width_of_cut_ae = $attributes['width_of_cut_ae'];
        $this->g_code = $attributes['g_code'] ?? '';
        $this->notes = $attributes['notes'];

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
            'feed_rate_vf' => $this->feed_rate_vf,
            'depth_of_cut_ap' => $this->depth_of_cut_ap,
            'width_of_cut_ae' => $this->width_of_cut_ae,
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
        $this->feed_rate_vf = $operation->feed_rate_vf;
        $this->depth_of_cut_ap = $operation->depth_of_cut_ap;
        $this->width_of_cut_ae = $operation->width_of_cut_ae;
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
        $this->feed_rate_vf = $operation->feed_rate_vf;
        $this->depth_of_cut_ap = $operation->depth_of_cut_ap;
        $this->width_of_cut_ae = $operation->width_of_cut_ae;
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
            'feed_rate_vf' => $this->feed_rate_vf,
            'depth_of_cut_ap' => $this->depth_of_cut_ap,
            'width_of_cut_ae' => $this->width_of_cut_ae,
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
