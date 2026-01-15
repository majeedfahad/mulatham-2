<?php

namespace App\Filament\Widgets;

use App\Models\Room;
use App\Models\RoomPlayer;
use App\Models\RoomQuestion;
use App\Models\Suggestion;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class GameStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalRooms = Room::count();
        $activeRooms = Room::where('status', 'playing')->count();
        $finishedRooms = Room::where('status', 'finished')->count();
        $totalPlayers = RoomPlayer::count();
        $totalQuestions = RoomQuestion::count();

        // Today stats
        $todayRooms = Room::whereDate('created_at', today())->count();
        $todayPlayers = RoomPlayer::whereDate('created_at', today())->count();

        // Suggestions stats
        $pendingSuggestions = Suggestion::pending()->count();
        $approvedSuggestions = Suggestion::approved()->count();

        return [
            Stat::make('Total Rooms', $totalRooms)
                ->description("$todayRooms today")
                ->descriptionIcon('heroicon-m-home')
                ->color('primary'),

            Stat::make('Active Games', $activeRooms)
                ->description("$finishedRooms finished")
                ->descriptionIcon('heroicon-m-play')
                ->color('success'),

            Stat::make('Total Players', $totalPlayers)
                ->description("$todayPlayers today")
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),

            Stat::make('Questions Asked', $totalQuestions)
                ->descriptionIcon('heroicon-m-question-mark-circle')
                ->color('warning'),

            Stat::make('Pending Suggestions', $pendingSuggestions)
                ->description("$approvedSuggestions approved")
                ->descriptionIcon('heroicon-m-light-bulb')
                ->color($pendingSuggestions > 0 ? 'danger' : 'success'),
        ];
    }
}
