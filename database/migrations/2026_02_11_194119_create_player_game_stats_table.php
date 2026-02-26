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
        Schema::create('player_game_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')->constrained()->cascadeOnDelete();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete()->index('idx_pgs_team');
            $table->foreignId('player_id')->constrained()->cascadeOnDelete()->index('idx_pgs_player');

            // Batting Stats
            $table->unsignedInteger('ao')->default(0)->comment('At Outs'); // Outs made
            $table->unsignedInteger('ab')->default(0)->comment('At Bats');
            $table->unsignedInteger('h')->default(0)->comment('Hits');
            $table->unsignedInteger('1b')->default(0)->comment('Singles');
            $table->unsignedInteger('2b')->default(0)->comment('Doubles');
            $table->unsignedInteger('3b')->default(0)->comment('Triples');
            $table->unsignedInteger('hr')->default(0)->comment('Home Runs');
            $table->unsignedInteger('r')->default(0)->comment('Runs');
            $table->unsignedInteger('rbi')->default(0)->comment('Runs Batted In');
            $table->unsignedInteger('bb')->default(0)->comment('Base on Balls');
            $table->unsignedInteger('so')->default(0)->comment('Strike Outs');
            $table->unsignedInteger('hbp')->default(0)->comment('Hit by Pitch');
            $table->unsignedInteger('sb')->default(0)->comment('Stolen Bases');
            $table->unsignedInteger('cs')->default(0)->comment('Caught Stealing');
            $table->unsignedInteger('sac')->default(0)->comment('Sacrifice Flies');
            $table->unsignedInteger('sf')->default(0)->comment('Sacrifice Hits');

            // Pitching Stats
            $table->decimal('ip', 4, 1)->default(0); // Innings Pitched
            $table->unsignedInteger('er')->default(0); // Earned Runs
            $table->unsignedInteger('p_h')->default(0)->comment('Hits Allowed');
            $table->unsignedInteger('p_r')->default(0)->comment('Runs Allowed');
            $table->unsignedInteger('p_bb')->default(0)->comment('Walks Allowed');
            $table->unsignedInteger('p_so')->default(0)->comment('Strikeouts');
            $table->unsignedInteger('p_hr')->default(0)->comment('Home Runs Allowed');
            $table->unsignedInteger('p_hbp')->default(0)->comment('Hit Batters');
            $table->unsignedInteger('p_wp')->default(0)->comment('Wild Pitches');
            $table->unsignedInteger('p_bk')->default(0)->comment('Balks');
            $table->unsignedInteger('w')->default(0)->comment('Win');
            $table->unsignedInteger('l')->default(0)->comment('Loss');
            $table->unsignedInteger('sv')->default(0)->comment('Save');
            $table->unsignedInteger('bs')->default(0)->comment('Blown Save');

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['game_id', 'player_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('player_game_stats');
    }
};