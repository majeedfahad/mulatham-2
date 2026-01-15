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
        Schema::create('suggestion_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('suggestion_id')->constrained()->cascadeOnDelete();
            $table->string('voter_session_id'); // Guest session tracking
            $table->timestamps();

            $table->unique(['suggestion_id', 'voter_session_id']); // Prevent double voting
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suggestion_votes');
    }
};
