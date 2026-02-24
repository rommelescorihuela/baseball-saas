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
        // Agregar created_by a players
        Schema::table('players', function (Blueprint $table) {
            $table->foreignId('created_by')->nullable()->after('league_id')->constrained('users')->nullOnDelete();
        });

        // Agregar created_by a game_events
        Schema::table('game_events', function (Blueprint $table) {
            $table->foreignId('created_by')->nullable()->after('runs_scored')->constrained('users')->nullOnDelete();
        });

        // Agregar created_by a player_game_stats
        Schema::table('player_game_stats', function (Blueprint $table) {
            $table->foreignId('created_by')->nullable()->after('er')->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('players', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn('created_by');
        });

        Schema::table('game_events', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn('created_by');
        });

        Schema::table('player_game_stats', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn('created_by');
        });
    }
};
