<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('player_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->constrained()->cascadeOnDelete();
            $table->foreignId('game_id')->constrained()->cascadeOnDelete();

            // Ejemplo de estadísticas ofensivas
            $table->integer('at_bats')->default(0);
            $table->integer('hits')->default(0);
            $table->integer('runs')->default(0);
            $table->integer('home_runs')->default(0);
            $table->integer('rbis')->default(0);
            $table->integer('walks')->default(0);
            $table->integer('strikeouts')->default(0);

            // Ejemplo de estadísticas defensivas (opcional)
            $table->integer('innings_pitched')->default(0);
            $table->integer('strikeouts_pitched')->default(0);
            $table->integer('runs_allowed')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('player_stats');
    }
};
