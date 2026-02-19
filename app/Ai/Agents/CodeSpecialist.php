<?php

namespace App\Ai\Agents;

use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Promptable;
use Stringable;
use App\Models\Tool as ToolModel;

class CodeSpecialist implements Agent, Conversational, HasTools
{
    use Promptable;

    public function __construct(
        public int $spindle_speed_n,
        public string $description,
        public ToolModel $tool,
        public ?float $feed_per_revolution_fn = null,
        public ?int $feed_rate_vf = null,
        public ?float $width_of_cut_ae = null,
        public float $depth_of_cut_ap,
    ) {}

    /**
     * Get the instructions that the agent should follow.
     */
    public function instructions(): Stringable|string
    {
        // Basic info
        $prompt = 'Jesteś programistą CAM. Generujesz czysty, bezpieczny G-kod ISO. Nie dodajesz żadnych komentarzy ani wyjaśnień – zwracasz tylko i wyłącznie kod G.';

        // Main instructions
        if ($this->description) {
            $prompt .= "Opis operacji: {$this->description}\n";
        }

        $prompt .= "Obroty (S): {$this->spindle_speed_n}\n";

        if ($this->tool->type === 'turning_tool') {
            $prompt .= "Posuw (F): {$this->feed_per_revolution_fn} mm/obrót (Użyj G95)\n";
        } else {
            $prompt .= "Posuw (F): {$this->feed_rate_vf} mm/min (Użyj G94)\n";
            $prompt .= "Średnica narzędzia: {$this->tool->diameter} mm\n";
            $prompt .= "Szerokość skrawania (AE): {$this->width_of_cut_ae} mm\n";
        }
        $prompt .= "Głębokość (AP): {$this->depth_of_cut_ap} mm\n";

        // Rules
        $prompt .= "
        ZASADY:
        1. START: Zawsze zaczynaj od G21 (milimetry), G90 (współrzędne absolutne) i G54 (układ bazowy).
        2. START WRZECIONA: Dobierz odpowiedni kierunek (M3 lub M4) na podstawie typu narzędzia i operacji. Zapisz jako: M[kierunek] S{$this->spindle_speed_n}.
        3. DOJAZD: Bezpieczny Z5, wejście w materiał G1 z posuwem F.
        4. NARZĘDZIE: Dla frezowania G43 H1.
        5. GEOMETRIA: Wygeneruj prostą ścieżkę narzędzia realizującą cel z opisu.
        6. KONIEC: Odjazd Z5, M30.
        7.Zwróć TYLKO czysty G-kod.";

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
        return [];
    }
}
