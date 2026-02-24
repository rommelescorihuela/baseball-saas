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
        Schema::table('player_season_stats', function (Blueprint $table) {
            // Additional Batting Stats
            if (! Schema::hasColumn('player_season_stats', 'rbi')) {
                $table->unsignedInteger('rbi')->default(0)->comment('Runs Batted In')->after('r');
            }
            if (! Schema::hasColumn('player_season_stats', 'bb')) {
                $table->unsignedInteger('bb')->default(0)->comment('Base on Balls (Walks)')->after('rbi');
            }
            if (! Schema::hasColumn('player_season_stats', 'hbp')) {
                $table->unsignedInteger('hbp')->default(0)->comment('Hit by Pitch')->after('bb');
            }
            if (! Schema::hasColumn('player_season_stats', 'sb')) {
                $table->unsignedInteger('sb')->default(0)->comment('Stolen Bases')->after('hbp');
            }
            if (! Schema::hasColumn('player_season_stats', 'cs')) {
                $table->unsignedInteger('cs')->default(0)->comment('Caught Stealing')->after('sb');
            }
            if (! Schema::hasColumn('player_season_stats', 'sac')) {
                $table->unsignedInteger('sac')->default(0)->comment('Sacrifice Flies')->after('cs');
            }
            if (! Schema::hasColumn('player_season_stats', 'sf')) {
                $table->unsignedInteger('sf')->default(0)->comment('Sacrifice Hits')->after('sac');
            }

            // Pitching Stats
            if (! Schema::hasColumn('player_season_stats', 'ip')) {
                $table->decimal('ip', 5, 1)->default(0)->comment('Innings Pitched')->after('sf');
            }
            if (! Schema::hasColumn('player_season_stats', 'er')) {
                $table->unsignedInteger('er')->default(0)->comment('Earned Runs')->after('ip');
            }
            if (! Schema::hasColumn('player_season_stats', 'p_h')) {
                $table->unsignedInteger('p_h')->default(0)->comment('Hits Allowed')->after('er');
            }
            if (! Schema::hasColumn('player_season_stats', 'p_r')) {
                $table->unsignedInteger('p_r')->default(0)->comment('Runs Allowed')->after('p_h');
            }
            if (! Schema::hasColumn('player_season_stats', 'p_bb')) {
                $table->unsignedInteger('p_bb')->default(0)->comment('Walks Allowed')->after('p_r');
            }
            if (! Schema::hasColumn('player_season_stats', 'p_so')) {
                $table->unsignedInteger('p_so')->default(0)->comment('Strikeouts')->after('p_bb');
            }
            if (! Schema::hasColumn('player_season_stats', 'p_hr')) {
                $table->unsignedInteger('p_hr')->default(0)->comment('Home Runs Allowed')->after('p_so');
            }
            if (! Schema::hasColumn('player_season_stats', 'w')) {
                $table->unsignedInteger('w')->default(0)->comment('Win')->after('p_hr');
            }
            if (! Schema::hasColumn('player_season_stats', 'l')) {
                $table->unsignedInteger('l')->default(0)->comment('Loss')->after('w');
            }
            if (! Schema::hasColumn('player_season_stats', 'sv')) {
                $table->unsignedInteger('sv')->default(0)->comment('Save')->after('l');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('player_season_stats', function (Blueprint $table) {
            $columns = [];
            foreach (['rbi', 'bb', 'hbp', 'sb', 'cs', 'sac', 'sf', 'ip', 'er', 'p_h', 'p_r', 'p_bb', 'p_so', 'p_hr', 'w', 'l', 'sv'] as $col) {
                if (Schema::hasColumn('player_season_stats', $col)) {
                    $columns[] = $col;
                }
            }
            if (! empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
