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
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('player_id')->constrained()->cascadeOnDelete();

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

            // Pitching Stats (Optional split later)
            $table->decimal('ip', 4, 1)->default(0); // Innings Pitched
            $table->unsignedInteger('er')->default(0); // Earned Runs

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