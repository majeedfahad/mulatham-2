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
        Schema::table('room_questions', function (Blueprint $table) {
            // Question type: 'text' for direct answer, 'choice' for multiple choice
            $table->enum('question_type', ['text', 'choice'])->default('text')->after('question_text');

            // For multiple choice: JSON array of 4 options
            // Format: ["option1", "option2", "option3", "option4"]
            $table->json('choices')->nullable()->after('question_type');

            // For multiple choice: index of correct answer (0-3)
            $table->tinyInteger('correct_choice_index')->nullable()->after('choices');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('room_questions', function (Blueprint $table) {
            $table->dropColumn(['question_type', 'choices', 'correct_choice_index']);
        });
    }
};
