<?php

namespace Database\Seeders;

use App\Models\Room;
use App\Models\RoomPlayer;
use App\Models\RoomQuestion;
use App\Models\RoomAnswer;
use App\Models\Reveal;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GameSeeder extends Seeder
{
    /**
     * Clear all game-related tables or create a test game.
     * Run: php artisan db:seed --class=GameSeeder
     * For test game: php artisan db:seed --class=GameSeeder -- --test
     */
    public function run(): void
    {
        // Disable foreign key checks to allow truncate
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Clear all game tables
        Reveal::truncate();
        RoomAnswer::truncate();
        RoomQuestion::truncate();
        RoomPlayer::truncate();
        Room::truncate();

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('Game tables cleared successfully!');

        // Check if we should create a test game
        if ($this->command->confirm('Do you want to create a test game with 3 players?', false)) {
            $this->createTestGame();
        } else {
            $this->command->info('Visit ' . config('app.url', 'http://mulatham-2.test') . ' to start a new game.');
        }
    }

    /**
     * Create a test game ready to play
     */
    private function createTestGame(): void
    {
        // Create room
        $room = Room::create([
            'code' => 'TEST01',
            'status' => 'lobby',
            'max_questions' => 5,
        ]);

        // Create 3 test players
        $players = [
            ['name' => 'أحمد', 'fake_name' => 'الفيلسوف', 'is_host' => true],
            ['name' => 'سارة', 'fake_name' => 'نجمة الليل', 'is_host' => false],
            ['name' => 'خالد', 'fake_name' => 'الغامض', 'is_host' => false],
        ];

        $tokens = [];
        foreach ($players as $player) {
            $token = RoomPlayer::generateToken();
            $tokens[$player['name']] = $token;

            RoomPlayer::create([
                'room_id' => $room->id,
                'name' => $player['name'],
                'fake_name' => $player['fake_name'],
                'is_host' => $player['is_host'],
                'status' => 'ready',
                'session_token' => $token,
            ]);
        }

        $this->command->info('');
        $this->command->info('=== Test Game Created ===');
        $this->command->info('Room Code: TEST01');
        $this->command->info('');
        $this->command->info('Open these URLs in different browser windows/incognito tabs:');
        $this->command->info('');

        $baseUrl = config('app.url', 'http://mulatham-2.test');

        foreach ($tokens as $name => $token) {
            $this->command->info("$name: $baseUrl/room/TEST01?token=$token");
        }

        $this->command->info('');
        $this->command->info('All players are marked as "ready". The host (أحمد) can start the game.');
    }
}
