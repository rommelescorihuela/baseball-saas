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
        // Modificar tabla teams
        Schema::table('teams', function (Blueprint $table) {
            $table->string('slug')->unique()->nullable()->after('name');
            $table->string('city')->nullable()->after('logo');
            $table->foreignId('league_id')->nullable()->change();
        });

        // Modificar tabla players
        Schema::table('players', function (Blueprint $table) {
            $table->foreignId('league_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropColumn('slug');
            $table->foreignId('league_id')->nullable(false)->change();
        });

        Schema::table('players', function (Blueprint $table) {
            $table->foreignId('league_id')->nullable(false)->change();
        });
    }
};
