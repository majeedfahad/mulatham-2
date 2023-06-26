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
        Schema::create('eliminations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attacker_id');
            $table->unsignedBigInteger('target_id');
            $table->string('status');
            $table->double('points');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eliminations');
    }
};
