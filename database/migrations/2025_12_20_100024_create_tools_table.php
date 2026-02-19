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
        Schema::create('tools', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('name'); // np. "Nóż zgrubny zewnętrzny"
            $table->string('type'); // frez, noz_tokarski, wiertlo
            $table->string('material'); // vhm, hss, weglik (plytka)

            $table->decimal('diameter', 8, 2)->nullable(); // Średnica
            $table->integer('flutes')->nullable(); // Liczba ostrzy (z)

            // Specyficzne dla noży
            $table->string('insert_shape')->nullable(); // c, d, s, t, w (kształt płytki)
            $table->string('insert_code')->nullable(); // np. "0804"

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tools');
    }
};
