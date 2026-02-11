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
        Schema::create('player_season_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('season_id')->constrained()->cascadeOnDelete();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('player_id')->constrained()->cascadeOnDelete();

            // Batting Stats Aggregate
            $table->unsignedInteger('g')->default(0)->comment('Games Played');
            $table->unsignedInteger('ab')->default(0);
            $table->unsignedInteger('h')->default(0);
            $table->unsignedInteger('1b')->default(0);
            $table->unsignedInteger('2b')->default(0);
            $table->unsignedInteger('3b')->default(0);
            $table->unsignedInteger('hr')->default(0);
            $table->unsignedInteger('r')->default(0);
            $table->unsignedInteger('rbi')->default(0);
            $table->unsignedInteger('bb')->default(0);
            $table->unsignedInteger('so')->default(0);

            $table->timestamps();

            $table->unique(['season_id', 'player_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('player_season_stats');
    }
};