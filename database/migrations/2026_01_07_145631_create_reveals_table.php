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
        Schema::create('reveals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->foreignId('room_question_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('guesser_id');
            $table->unsignedBigInteger('target_id');
            $table->unsignedBigInteger('guessed_player_id');
            $table->boolean('is_correct');
            $table->integer('points_transferred')->default(0);
            $table->timestamps();

            $table->foreign('guesser_id')->references('id')->on('room_players')->onDelete('cascade');
            $table->foreign('target_id')->references('id')->on('room_players')->onDelete('cascade');
            $table->foreign('guessed_player_id')->references('id')->on('room_players')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reveals');
    }
};
