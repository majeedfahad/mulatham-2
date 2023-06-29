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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('fakename')->unique()->nullable();
            $table->string('password');
            $table->double('score')->default(0);
            $table->double('hidden_score')->nullable();
            $table->integer('status')->default(1);
            $table->integer('role')->default(0);
            $table->integer('order')->default(0);
            $table->rememberToken();
            $table->timestamps();
        });

        DB::table('users')->insert([
            'name' => 'Admin',
            'fakename' => 'Admin',
            'password' => \Illuminate\Support\Facades\Hash::make('0544402788@a'),
            'role' => 1
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
