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
            // Round number - all players write questions for the same round
            $table->integer('round_number')->default(1)->after('question_order');

            // Whether this question was selected to be answered in this round
            $table->boolean('is_selected')->default(false)->after('round_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('room_questions', function (Blueprint $table) {
            $table->dropColumn(['round_number', 'is_selected']);
        });
    }
};
