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
        Schema::create('room_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_question_id')->constrained()->onDelete('cascade');
            $table->foreignId('room_player_id')->constrained()->onDelete('cascade');
            $table->text('answer');
            $table->boolean('is_correct')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_answers');
    }
};
