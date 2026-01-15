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
        Schema::create('suggestions', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('author_name')->nullable();
            $table->string('author_session_id'); // Guest session tracking
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->enum('category', ['feature', 'bug', 'improvement', 'other'])->default('feature');
            $table->integer('votes_count')->default(0);
            $table->foreignId('room_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suggestions');
    }
};
