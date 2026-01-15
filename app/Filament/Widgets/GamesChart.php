<?php

namespace App\Filament\Widgets;

use App\Models\Room;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class GamesChart extends ChartWidget
{
    protected static ?string $heading = 'Games Over Time';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $data = $this->getGamesPerDay();

        return [
            'datasets' => [
                [
                    'label' => 'Games Created',
                    'data' => $data['gamesPerDay'],
                    'backgroundColor' => 'rgba(201, 162, 39, 0.5)',
                    'borderColor' => 'rgb(201, 162, 39)',
                    'fill' => true,
                ],
                [
                    'label' => 'Games Finished',
                    'data' => $data['finishedPerDay'],
                    'backgroundColor' => 'rgba(34, 197, 94, 0.5)',
                    'borderColor' => 'rgb(34, 197, 94)',
                    'fill' => true,
                ],
            ],
            'labels' => $data['labels'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    private function getGamesPerDay(): array
    {
        $days = 14;
        $labels = [];
        $gamesPerDay = [];
        $finishedPerDay = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $labels[] = $date->format('M d');

            $gamesPerDay[] = Room::whereDate('created_at', $date)->count();
            $finishedPerDay[] = Room::whereDate('created_at', $date)
                ->where('status', 'finished')
                ->count();
        }

        return [
            'labels' => $labels,
            'gamesPerDay' => $gamesPerDay,
            'finishedPerDay' => $finishedPerDay,
        ];
    }
}
