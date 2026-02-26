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
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->foreignId('league_id')->constrained()->cascadeOnDelete()->index('idx_games_league');
            $table->foreignId('competition_id')->nullable()->constrained()->cascadeOnDelete()->index('idx_games_comp');
            $table->foreignId('category_id')->constrained()->cascadeOnDelete()->index('idx_games_cat');
            $table->foreignId('home_team_id')->constrained('teams')->cascadeOnDelete();
            $table->foreignId('visitor_team_id')->constrained('teams')->cascadeOnDelete();
            $table->timestamp('start_time')->index('idx_games_start');
            $table->string('location')->nullable();
            $table->enum('status', ['scheduled', 'in_progress', 'finished', 'suspended', 'voided'])->default('scheduled')->index('idx_games_status');
            $table->integer('home_score')->default(0);
            $table->integer('visitor_score')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};