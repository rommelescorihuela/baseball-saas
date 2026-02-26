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
        Schema::create('leagues', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('logo')->nullable();
            $table->enum('status', ['active', 'past_due', 'suspended'])->default('active');


            $table->string('plan')->default('free'); // free, pro, unlimited
            $table->string('subscription_status')->default('active'); // active, trialing, past_due, canceled
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('subscription_ends_at')->nullable();

            $table->timestamps();
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {


        // 2. Luego borramos al Padre
        Schema::dropIfExists('leagues');
    }
};