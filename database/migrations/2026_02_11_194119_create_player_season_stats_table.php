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
            $table->unsignedInteger('singles')->default(0);
            $table->unsignedInteger('doubles')->default(0);
            $table->unsignedInteger('triples')->default(0);
            $table->unsignedInteger('hr')->default(0);
            $table->unsignedInteger('r')->default(0);
            $table->unsignedInteger('rbi')->default(0);
            $table->unsignedInteger('bb')->default(0);
            $table->unsignedInteger('so')->default(0);
            $table->unsignedInteger('hbp')->default(0)->comment('Hit by Pitch');
            $table->unsignedInteger('sb')->default(0)->comment('Stolen Bases');
            $table->unsignedInteger('cs')->default(0)->comment('Caught Stealing');
            $table->unsignedInteger('sh')->default(0)->comment('Sacrifice Hits');
            $table->unsignedInteger('sacrifice_flies')->default(0)->comment('Sacrifice Flies');

            // Pitching Stats
            $table->decimal('ip', 5, 1)->default(0)->comment('Innings Pitched');
            $table->unsignedInteger('p_er')->default(0)->comment('Earned Runs');
            $table->unsignedInteger('p_h')->default(0)->comment('Hits Allowed');
            $table->unsignedInteger('p_r')->default(0)->comment('Runs Allowed');
            $table->unsignedInteger('p_bb')->default(0)->comment('Walks Allowed');
            $table->unsignedInteger('p_so')->default(0)->comment('Strikeouts');
            $table->unsignedInteger('p_hr')->default(0)->comment('Home Runs Allowed');
            $table->unsignedInteger('w')->default(0)->comment('Win');
            $table->unsignedInteger('l')->default(0)->comment('Loss');
            $table->unsignedInteger('sv')->default(0)->comment('Save');

            $table->timestamps();

            $table->unique(['season_id', 'player_id', 'team_id']);
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