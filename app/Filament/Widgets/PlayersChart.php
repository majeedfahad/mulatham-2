<?php

namespace App\Filament\Widgets;

use App\Models\RoomPlayer;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class PlayersChart extends ChartWidget
{
    protected static ?string $heading = 'Players Joined';

    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $data = $this->getPlayersPerDay();

        return [
            'datasets' => [
                [
                    'label' => 'Players',
                    'data' => $data['playersPerDay'],
                    'backgroundColor' => 'rgba(99, 102, 241, 0.5)',
                    'borderColor' => 'rgb(99, 102, 241)',
                    'fill' => true,
                ],
            ],
            'labels' => $data['labels'],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    private function getPlayersPerDay(): array
    {
        $days = 14;
        $labels = [];
        $playersPerDay = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $labels[] = $date->format('M d');

            $playersPerDay[] = RoomPlayer::whereDate('created_at', $date)->count();
        }

        return [
            'labels' => $labels,
            'playersPerDay' => $playersPerDay,
        ];
    }
}
