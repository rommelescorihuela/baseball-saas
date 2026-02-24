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
        Schema::table('player_game_stats', function (Blueprint $table) {
            // Additional Batting Stats
            if (! Schema::hasColumn('player_game_stats', 'rbi')) {
                $table->unsignedInteger('rbi')->default(0)->comment('Runs Batted In')->after('r');
            }
            if (! Schema::hasColumn('player_game_stats', 'bb')) {
                $table->unsignedInteger('bb')->default(0)->comment('Base on Balls (Walks)')->after('rbi');
            }
            if (! Schema::hasColumn('player_game_stats', 'hbp')) {
                $table->unsignedInteger('hbp')->default(0)->comment('Hit by Pitch')->after('bb');
            }
            if (! Schema::hasColumn('player_game_stats', 'sb')) {
                $table->unsignedInteger('sb')->default(0)->comment('Stolen Bases')->after('hbp');
            }
            if (! Schema::hasColumn('player_game_stats', 'cs')) {
                $table->unsignedInteger('cs')->default(0)->comment('Caught Stealing')->after('sb');
            }
            if (! Schema::hasColumn('player_game_stats', 'sac')) {
                $table->unsignedInteger('sac')->default(0)->comment('Sacrifice Flies')->after('cs');
            }
            if (! Schema::hasColumn('player_game_stats', 'sf')) {
                $table->unsignedInteger('sf')->default(0)->comment('Sacrifice Hits')->after('sac');
            }

            // Extended Pitching Stats
            if (! Schema::hasColumn('player_game_stats', 'p_h')) {
                $table->unsignedInteger('p_h')->default(0)->comment('Hits Allowed')->after('ip');
            }
            if (! Schema::hasColumn('player_game_stats', 'p_r')) {
                $table->unsignedInteger('p_r')->default(0)->comment('Runs Allowed')->after('p_h');
            }
            if (! Schema::hasColumn('player_game_stats', 'p_bb')) {
                $table->unsignedInteger('p_bb')->default(0)->comment('Walks Allowed')->after('p_r');
            }
            if (! Schema::hasColumn('player_game_stats', 'p_so')) {
                $table->unsignedInteger('p_so')->default(0)->comment('Strikeouts')->after('p_bb');
            }
            if (! Schema::hasColumn('player_game_stats', 'p_hr')) {
                $table->unsignedInteger('p_hr')->default(0)->comment('Home Runs Allowed')->after('p_so');
            }
            if (! Schema::hasColumn('player_game_stats', 'p_hbp')) {
                $table->unsignedInteger('p_hbp')->default(0)->comment('Hit Batters')->after('p_hr');
            }
            if (! Schema::hasColumn('player_game_stats', 'p_wp')) {
                $table->unsignedInteger('p_wp')->default(0)->comment('Wild Pitches')->after('p_hbp');
            }
            if (! Schema::hasColumn('player_game_stats', 'p_bk')) {
                $table->unsignedInteger('p_bk')->default(0)->comment('Balks')->after('p_wp');
            }
            if (! Schema::hasColumn('player_game_stats', 'w')) {
                $table->unsignedInteger('w')->default(0)->comment('Win')->after('p_bk');
            }
            if (! Schema::hasColumn('player_game_stats', 'l')) {
                $table->unsignedInteger('l')->default(0)->comment('Loss')->after('w');
            }
            if (! Schema::hasColumn('player_game_stats', 'sv')) {
                $table->unsignedInteger('sv')->default(0)->comment('Save')->after('l');
            }
            if (! Schema::hasColumn('player_game_stats', 'bs')) {
                $table->unsignedInteger('bs')->default(0)->comment('Blown Save')->after('sv');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('player_game_stats', function (Blueprint $table) {
            $columns = [];
            foreach (['rbi', 'bb', 'hbp', 'sb', 'cs', 'sac', 'sf', 'p_h', 'p_r', 'p_bb', 'p_so', 'p_hr', 'p_hbp', 'p_wp', 'p_bk', 'w', 'l', 'sv', 'bs'] as $col) {
                if (Schema::hasColumn('player_game_stats', $col)) {
                    $columns[] = $col;
                }
            }
            if (! empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
