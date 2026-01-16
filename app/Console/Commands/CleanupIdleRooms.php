<?php

namespace App\Console\Commands;

use App\Events\GameStateUpdated;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CleanupIdleRooms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rooms:cleanup-idle {--hours=1 : Hours of inactivity before cleanup}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Finish rooms that have been idle (no question activity) for more than a specified time';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $hours = (int) $this->option('hours');
        $threshold = Carbon::now()->subHours($hours);

        $this->info("Looking for rooms idle for more than {$hours} hour(s)...");

        // Get active rooms (lobby or playing) that haven't been updated recently
        $idleRooms = Room::whereIn('status', ['lobby', 'playing'])
            ->where('updated_at', '<', $threshold)
            ->get();

        if ($idleRooms->isEmpty()) {
            $this->info('No idle rooms found.');

            return self::SUCCESS;
        }

        $this->info("Found {$idleRooms->count()} idle room(s) to clean up.");

        foreach ($idleRooms as $room) {
            // Double check: look at the latest question activity
            $lastQuestionActivity = $room->questions()->max('updated_at');
            $lastAnswerActivity = null;

            // Check answers on questions
            foreach ($room->questions as $question) {
                $questionLastAnswer = $question->answers()->max('updated_at');
                if ($questionLastAnswer && (! $lastAnswerActivity || $questionLastAnswer > $lastAnswerActivity)) {
                    $lastAnswerActivity = $questionLastAnswer;
                }
            }

            // Get the most recent activity
            $lastActivity = max(
                $room->updated_at,
                $lastQuestionActivity ? Carbon::parse($lastQuestionActivity) : null,
                $lastAnswerActivity ? Carbon::parse($lastAnswerActivity) : null
            );

            // If still idle after checking all activity
            if ($lastActivity && $lastActivity < $threshold) {
                $this->line("  - Room {$room->code}: Last activity at {$lastActivity->format('Y-m-d H:i:s')}");

                // Mark room as finished
                $room->update([
                    'status' => 'finished',
                    'phase' => null,
                ]);

                // Broadcast to any connected players
                try {
                    broadcast(new GameStateUpdated($room, 'game_ended', [
                        'reason' => 'idle_timeout',
                        'message' => 'تم إنهاء اللعبة بسبب عدم النشاط',
                        'redirect_url' => route('game.results', $room->code),
                    ]));
                } catch (\Exception $e) {
                    $this->warn("  Could not broadcast to room {$room->code}: {$e->getMessage()}");
                }

                $this->info("  ✓ Room {$room->code} marked as finished.");
            } else {
                $this->line("  - Room {$room->code}: Has recent activity, skipping.");
            }
        }

        $this->info('Cleanup complete.');

        return self::SUCCESS;
    }
}
