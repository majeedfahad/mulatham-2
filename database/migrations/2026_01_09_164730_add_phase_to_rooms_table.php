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
        Schema::table('rooms', function (Blueprint $table) {
            // Game phase: question_bank, answering, revealing
            // This is different from 'status' (lobby, playing, finished)
            $table->enum('phase', ['question_bank', 'answering', 'revealing'])->nullable()->after('status');

            // When the question bank phase started (for timer)
            $table->timestamp('question_bank_started_at')->nullable()->after('phase');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn(['phase', 'question_bank_started_at']);
        });
    }
};
