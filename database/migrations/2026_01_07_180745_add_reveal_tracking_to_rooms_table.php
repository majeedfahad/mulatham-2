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
            $table->foreignId('revealing_player_id')->nullable()->constrained('room_players')->onDelete('set null');
            $table->timestamp('reveal_started_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropForeign(['revealing_player_id']);
            $table->dropColumn(['revealing_player_id', 'reveal_started_at']);
        });
    }
};
