<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('game_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('inning');
            $table->boolean('is_top_inning');
            $table->foreignId('team_id')->constrained()->cascadeOnDelete()->comment('Offensive Team'); // Equipo al bate
            $table->foreignId('batter_id')->constrained('players')->cascadeOnDelete();
            $table->foreignId('pitcher_id')->constrained('players')->cascadeOnDelete();

            // Estado antes del evento
            $table->unsignedTinyInteger('outs_before')->default(0);
            $table->unsignedTinyInteger('balls_before')->default(0);
            $table->unsignedTinyInteger('strikes_before')->default(0);

            // El evento en sí
            $table->string('type'); // pitch, hit, out, substitution...
            $table->json('result')->nullable(); // Detalles: { kind: "strike", description: "Called Strike" }

            // Consecuencias
            $table->unsignedTinyInteger('runs_scored')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_events');
    }
};