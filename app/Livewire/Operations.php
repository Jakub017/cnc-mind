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
    public $feed_per_revolution_fn = null;
    public $feed_rate_vf = null;
    public $depth_of_cut_ap = null;
    public $width_of_cut_ae = null;
    public $theoretical_roughness_ra = null;
    public $g_code = '';
    public $want_g_code = false;
    public $notes = '';

    public function addOperation()
    {
        $validated = $this->validate();

        $tool = Tool::find($validated['tool_id']);
        $material = Material::find($validated['material_id']);

        $prompt = "
        Jesteś ekspertem technologii CNC. Twoim zadaniem jest obliczenie parametrów skrawania i wygenerowanie G-kodu.
        
        DANE WEJŚCIOWE:
        OPIS/CEL OPERACJI: {$validated['description']}. 
        NARZĘDZIE:
        - Typ: {$tool->typePromptLabel()}
        - Materiał: {$tool->materialPromptLabel()}
        - Średnica (D) [mm]: " . ($tool->diameter ?? 'Brak, narzędzie to nóż tokarski.') . "
        - Liczba ostrzy (z): " . ($tool->flutes) . "
        " . ($tool->insert_shape ? "- Kształt płytki: {$tool->insertShapePromptLabel()}\n" : "") . "
        " . ($tool->insert_code ? "- Kod płytki: {$tool->insert_code} (zidentyfikuj promień naroża rε z dwóch ostatnich cyfr, np. 04 = 0.4mm)\n" : "") . "

        MATERIAŁ OBRABIANY:
        - Kategoria: {$material->categoryPromptLabel()}
        - Podkategoria: {$material->subCategoryPromptLabel()}
        - Twardość: {$material->hardness_value} {$material->hardness_unit}
        " . ($material->notes ? "- Uwagi: {$material->notes}\n" : "") . "

        LOGIKA EKSPERCKA:
        1. KLASYFIKACJA ISO: Dobieraj vc i fz rygorystycznie według grup ISO (P, M, K, N, S, H).
        2. WPŁYW TWARDOŚCI: Skaluj vc odwrotnie proporcjonalnie do twardości (wyższa wartość HB/HRC = niższa prędkość vc). 
        3. STRATEGIA SAFETY FIRST: Jeśli użytkownik nie określił strategii 'High-Speed', zawsze wybieraj vc ze środka dolnego zakresu katalogowego dla danej grupy ISO, aby zapewnić bezpieczeństwo narzędzia.
        4. STALE TRUDNOOBRABIALNE: Dla ISO M (nierdzewne) i ISO S (superstopy), zachowaj szczególną ostrożność – vc rzadko przekracza 120 m/min dla standardowych narzędzi VHM.
        5. GEOMETRIA TOCZENIA: Jeśli rε nie jest podane, przyjmij rε = 0.4mm (standard). Oblicz Ra = (fn^2 / (32 * rε)) * 1000.
        6. ŚREDNICA ROBOCZA (D): 
        - We frezowaniu D to zawsze średnica narzędzia. 
        - W toczeniu D to średnica detalu z opisu (np. 40mm).
        Zawsze zwracaj tę wartość w polu 'effective_diameter'.
        7. GEOMETRIA FREZOWANIA: 
        - ap (Głębokość) to głębokość osiowa (wzdłuż osi narzędzia). Jeśli opis mówi o głębokości 10mm, ap powinno wynosić 10mm.
        - ae (Szerokość) to naddatek promieniowy. Dla wykańczania przyjmij 0.1 - 0.5 mm.
        8. GEOMETRIA FREZOWANIA: Zawsze uwzględniaj Liczbę Ostrzy (z) przy obliczaniu vf. vf = n * fz * z.
        9. JEDNOSTKI Ra: Chropowatość theoretical_roughness_ra podawaj zawsze w mikrometrach (μm).

        ZASADY OBLICZEŃ:
        1. n = (vc * 1000) / (π * D)
        2. vf = n * fz * z (frezowanie) LUB vf = n * fn (toczenie)
        ";

        $properties = [
        'cutting_speed_vc' => ['type' => 'number'],
        'spindle_speed_n' => ['type' => 'number'],
        'depth_of_cut_ap' => ['type' => 'number'],
        'effective_diameter' => ['type' => 'number'],
        'notes' => ['type' => 'string'],
        ];

        $required = [
            'cutting_speed_vc', 'spindle_speed_n', 'depth_of_cut_ap', 'effective_diameter', 'notes'
        ];

        if ($tool->type == 'turning_tool') {
            $properties['feed_per_revolution_fn'] = ['type' => 'number'];
            $properties['theoretical_roughness_ra'] = ['type' => 'number'];
            $required[] = 'feed_per_revolution_fn';
            $required[] = 'theoretical_roughness_ra';
        } else {
            $properties['feed_per_tooth_fz'] = ['type' => 'number'];
            $properties['width_of_cut_ae'] = ['type' => 'number'];
            $required[] = 'feed_per_tooth_fz';
            $required[] = 'width_of_cut_ae';
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

        $this->cutting_speed_vc = round($attributes['cutting_speed_vc'], 2);
        $this->spindle_speed_n = round(($this->cutting_speed_vc * 1000) / (M_PI * $attributes['effective_diameter']), 0);
        $this->feed_per_tooth_fz = isset($attributes['feed_per_tooth_fz']) ? round($attributes['feed_per_tooth_fz'], 4) : null;
        $this->feed_per_revolution_fn = isset($attributes['feed_per_revolution_fn']) ? round($attributes['feed_per_revolution_fn'], 4) : null;
        if ($tool->type == 'turning_tool') {
            $this->feed_rate_vf = round(($this->feed_per_revolution_fn ?? 0) * $this->spindle_speed_n, 0);
        } else {
            $this->feed_rate_vf = round(($this->feed_per_tooth_fz ?? 0) * ($tool->flutes ?? 1) * $this->spindle_speed_n, 0);
        }
        $this->depth_of_cut_ap = round($attributes['depth_of_cut_ap'], 2);
        $this->width_of_cut_ae = isset($attributes['width_of_cut_ae']) ? round($attributes['width_of_cut_ae'], 2) : null;
        $this->theoretical_roughness_ra = isset($attributes['theoretical_roughness_ra']) ? round($attributes['theoretical_roughness_ra'], 2) : null;
        $this->notes = $attributes['notes'];

        if($this->want_g_code) {
            $prompt_for_g_code = "";

            if($this->description) {
                $prompt_for_g_code .= "Opis operacji: {$this->description}\n";
            }
            $prompt_for_g_code .= "Obroty (S): {$this->spindle_speed_n}\n";
            if($tool->type === 'turning_tool') {
                $prompt_for_g_code .= "Posuw (F): {$this->feed_per_revolution_fn} mm/obrót (Użyj G95)\n";
            } else {
                $prompt_for_g_code .= "Posuw (F): {$this->feed_rate_vf} mm/min (Użyj G94)\n";
                $prompt_for_g_code .= "Średnica narzędzia: {$tool->diameter} mm\n";
                $prompt_for_g_code .= "Szerokość skrawania (AE): {$this->width_of_cut_ae} mm\n";
            }
            $prompt_for_g_code .= "Głębokość (AP): {$this->depth_of_cut_ap} mm\n";

            $prompt_for_g_code .= "
            ZASADY G-CODE:
            1. START: G21, G90. " . ($tool->type === 'turning_tool' ? 'G18, G95' : 'G17, G94') . ".
            2. START WRZECIONA: Dobierz odpowiedni kierunek (M3 lub M4) na podstawie typu narzędzia i operacji. Zapisz jako: M[kierunek] S{$this->spindle_speed_n}.
            3. DOJAZD: Bezpieczny Z5, wejście w materiał G1 z posuwem F.
            4. NARZĘDZIE: Dla frezowania G43 H1.
            5. GEOMETRIA: Wygeneruj prostą ścieżkę narzędzia realizującą cel z opisu.
            6. KONIEC: Odjazd Z5, M30.
            Zwróć TYLKO czysty G-kod.";

            $g_code_response = OpenAI::chat()->create([
                'model' => 'gpt-4o',
                'messages' => [
                    ['role' => 'system', 'content' => "Jesteś programistą CAM. Generujesz czysty, bezpieczny G-kod ISO. Nie dodajesz żadnych komentarzy ani wyjaśnień – zwracasz tylko i wyłącznie kod G."],
                    ['role' => 'user', 'content' => $prompt_for_g_code],
                ],
            ]);
            $raw_g_code = $g_code_response['choices'][0]['message']['content'];

            $this->g_code = str_replace(['```gcode', '```'], "", $raw_g_code);
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
