<?php

namespace App\Jobs;

use App\Ai\Agents\CodeSpecialist;
use App\Ai\Agents\ParametersSpecialist;
use App\Models\Material;
use App\Models\Tool;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CreateOperation implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public object $operation,
        public int $userId,
        public array $validated,
        public bool $wantGCode = false,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $tool = Tool::find($this->validated['tool_id']);
        $material = Material::find($this->validated['material_id']);

        if ($this->validated['file_id']) {
            $parametersAgent = new ParametersSpecialist(
                tool: $tool,
                material: $material,
                description: $this->validated['description'] ?? '',
                file_id: $this->validated['file_id'] ?? null,
                store_id: auth()->user()->vectorStore->google_id,
            );
            $parametersResponse = $parametersAgent->prompt('Przeanalizuj te dane i oblicz optymalne parametry skrawania dla tej operacji. UWAGA: Otrzymałeś plik, skorzystaj z narzędzia (tool) FileSearch w celu uzyskania z pliku jeszcze dokładniejszych danych. ');
        } else {
            $parametersAgent = new ParametersSpecialist(
                tool: $tool,
                material: $material,
                description: $this->validated['description'] ?? '',
            );
            $parametersResponse = $parametersAgent->prompt('Przeanalizuj te dane i oblicz optymalne parametry skrawania dla tej operacji.');
        }

        $cutting_speed_vc = round($parametersResponse['cutting_speed_vc'], 2);
        $spindle_speed_n = round(($cutting_speed_vc * 1000) / (M_PI * $parametersResponse['effective_diameter']), 0);
        $feed_per_tooth_fz = isset($parametersResponse['feed_per_tooth_fz']) ? round($parametersResponse['feed_per_tooth_fz'], 4) : null;
        $feed_per_revolution_fn = isset($parametersResponse['feed_per_revolution_fn']) ? round($parametersResponse['feed_per_revolution_fn'], 4) : null;
        if ($tool->type == 'turning_tool') {
            $feed_rate_vf = round(($feed_per_revolution_fn ?? 0) * $spindle_speed_n, 0);
        } else {
            $feed_rate_vf = round(($feed_per_tooth_fz ?? 0) * ($tool->flutes ?? 1) * $spindle_speed_n, 0);
        }
        $depth_of_cut_ap = round($parametersResponse['depth_of_cut_ap'], 2);
        $width_of_cut_ae = isset($parametersResponse['width_of_cut_ae']) ? round($parametersResponse['width_of_cut_ae'], 2) : null;
        $theoretical_roughness_ra = isset($parametersResponse['theoretical_roughness_ra']) ? round($parametersResponse['theoretical_roughness_ra'], 2) : null;
        $notes = $parametersResponse['notes'];

        if ($this->wantGCode != false) {
            $codeAgent = new CodeSpecialist(
                spindle_speed_n: $spindle_speed_n,
                description: $this->validated['description'] ?? '',
                tool: $tool,
                feed_per_revolution_fn: $feed_per_revolution_fn,
                feed_rate_vf: $feed_rate_vf,
                width_of_cut_ae: $width_of_cut_ae,
                depth_of_cut_ap: $depth_of_cut_ap,
            );

            $codeResponse = $codeAgent->prompt('Przygotuj G-Code dla tej operacji.');

            $g_code = str_replace(['```gcode', '```'], '', $codeResponse);
        }

        $this->operation->update([
            'user_id' => $this->userId,
            'name' => $this->validated['name'],
            'description' => $this->validated['description'] ?? '',
            'tool_id' => $this->validated['tool_id'],
            'material_id' => $this->validated['material_id'],
            'file_id' => $this->validated['file_id'] ?? null,
            'cutting_speed_vc' => $cutting_speed_vc,
            'spindle_speed_n' => $spindle_speed_n,
            'feed_per_tooth_fz' => $feed_per_tooth_fz,
            'feed_per_revolution_fn' => $feed_per_revolution_fn,
            'feed_rate_vf' => $feed_rate_vf,
            'depth_of_cut_ap' => $depth_of_cut_ap,
            'width_of_cut_ae' => $width_of_cut_ae,
            'theoretical_roughness_ra' => $theoretical_roughness_ra,
            'g_code' => $g_code ?? '',
            'notes' => $notes ?? '',
            'status' => 'completed',
        ]);

        $this->operation->save();
    }
}
