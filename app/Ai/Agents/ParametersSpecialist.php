<?php

namespace App\Ai\Agents;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Promptable;
use Stringable;
use App\Models\Tool as ToolModel;
use App\Models\Material;
use Laravel\Ai\Providers\Tools\FileSearch;

class ParametersSpecialist implements Agent, Conversational, HasStructuredOutput, HasTools
{
    use Promptable;

    public function __construct(
        public ToolModel $tool,
        public Material $material,
        public string $description,
        public ?int $file_id = null,
        public ?string $store_id = '',
    ) {}
    /**
     * Get the instructions that the agent should follow.
     */
    public function instructions(): Stringable|string
    {
        $toolDiameter = $this->tool->diameter ?? 'Brak, narzędzie to nóż tokarski.';
        $insertShape = $this->tool->insertShapePromptLabel();
        $insertCode = $this->tool->insert_code;

        // Basic info
        $prompt = 'Jesteś ekspertem technologii CNC. Twoim zadaniem jest obliczenie parametrów skrawania.';
        $prompt .= "
        DANE WEJŚCIOWE:
        OPIS/CEL OPERACJI: {$this->description}.";

        // Tool
        $prompt .= "NARZĘDZIE:
        - Typ: {$this->tool->type},
        - Materiał: {$this->tool->materialPromptLabel()}
        - Średnica (D) [mm]: {$toolDiameter},";

        if ($this->tool->type == 'turning_tool') {
            $prompt .= " - Liczba ostrzy (z): 1 (nóż tokarski) \n";
        } else {
            $prompt .= " - Liczba ostrzy (z): {$this->tool->flutes} \n";
        }

        if ($insertShape) {
            $prompt .= "- Kształt płytki: {$insertShape}\n";
        }

        if ($insertCode) {
            $prompt .= "- Kod płytki: {$insertCode} (zidentyfikuj promień naroża rε z dwóch ostatnich cyfr, np. 04 = 0.4mm)\n";
        }

        // Material
        $prompt .= "
        MATERIAŁ OBRABIANY:
        - Kategoria: {$this->material->categoryPromptLabel()}
        - Podkategoria: {$this->material->subCategoryPromptLabel()}
        - Twardość: {$this->material->hardness_value} {$this->material->hardness_unit}";

        // Final instructions
        $prompt .= "
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

        TWOIM PRIORYTETEM JEST: 
        1. Spójność matematyczna. 
        2. Bezpieczeństwo narzędzia (brak kolizji, odpowiednie wejście w materiał). 
        3. Krótka (maksymalnie 2 zdania) techniczna notatka w języku ".app()->getLocale().'.
        4. W notatce zapisz tylko ogólne podsumowanie, nie pisz żadnych liczb.';

        return $prompt;
    }

    /**
     * Get the list of messages comprising the conversation so far.
     */
    public function messages(): iterable
    {
        return [];
    }

    /**
     * Get the tools available to the agent.
     *
     * @return Tool[]
     */
    public function tools(): iterable
    {
        if($this->file_id == null) {
            return [];
        } else {
            return [
                new FileSearch(stores: [$this->store_id], where: [
                    'file_id' => $this->file_id,
                ]),
            ];
        }
    }

    /**
     * Get the agent's structured output schema definition.
     */
    public function schema(JsonSchema $schema): array
    {
        if ($this->tool->type == 'turning_tool') {
            return [
                'cutting_speed_vc' => $schema->number()->required(),
                'spindle_speed_n' => $schema->number()->required(),
                'depth_of_cut_ap' => $schema->number()->required(),
                'effective_diameter' => $schema->number()->required(),
                'feed_per_revolution_fn' => $schema->number()->required(),
                'theoretical_roughness_ra' => $schema->number()->required(),
                'notes' => $schema->string()->required(),
            ];
        } else {
            return [
                'cutting_speed_vc' => $schema->number()->required(),
                'spindle_speed_n' => $schema->number()->required(),
                'depth_of_cut_ap' => $schema->number()->required(),
                'effective_diameter' => $schema->number()->required(),
                'feed_per_tooth_fz' => $schema->number()->required(),
                'width_of_cut_ae' => $schema->number()->required(),
                'notes' => $schema->string()->required(),
            ];
        }
    }
}
