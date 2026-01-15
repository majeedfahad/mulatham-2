<?php

namespace App\Console\Commands;

use App\Models\Room;
use App\Models\RoomPlayer;
use App\Models\RoomQuestion;
use App\Models\Suggestion;
use App\Services\TelegramService;
use Illuminate\Console\Command;

class SendDailyTelegramReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:daily-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily statistics report to Telegram';

    /**
     * Execute the console command.
     */
    public function handle(TelegramService $telegram): int
    {
        if (! $telegram->isEnabled()) {
            $this->warn('Telegram notifications are disabled.');

            return self::SUCCESS;
        }

        $stats = [
            'games_today' => Room::whereDate('created_at', today())->count(),
            'games_finished_today' => Room::whereDate('created_at', today())
                ->where('status', 'finished')
                ->count(),
            'games_active' => Room::where('status', 'playing')->count(),
            'games_total' => Room::where('status', 'finished')->count(),
            'rooms_total' => Room::count(),
            'players_today' => RoomPlayer::whereDate('created_at', today())->count(),
            'players_total' => RoomPlayer::count(),
            'questions_today' => RoomQuestion::whereDate('created_at', today())->count(),
            'questions_total' => RoomQuestion::count(),
            'suggestions_pending' => Suggestion::pending()->count(),
            'suggestions_approved' => Suggestion::approved()->count(),
        ];

        $success = $telegram->sendDailyReport($stats);

        if ($success) {
            $this->info('Daily report sent successfully to Telegram.');

            return self::SUCCESS;
        }

        $this->error('Failed to send daily report to Telegram.');

        return self::FAILURE;
    }
}
