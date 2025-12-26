<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('operations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('tool_id')->constrained()->cascadeOnDelete();
            $table->foreignId('material_id')->constrained()->cascadeOnDelete();

            $table->decimal('cutting_speed_vc', 8, 2)->nullable();
            $table->unsignedInteger('spindle_speed_n')->nullable();
            $table->decimal('feed_per_tooth_fz', 8, 4)->nullable();
            $table->unsignedInteger('feed_rate_vf')->nullable();

            $table->decimal('depth_of_cut_ap', 8, 2)->nullable();
            $table->decimal('width_of_cut_ae', 8, 2)->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operations');
    }
};
