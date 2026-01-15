<?php

namespace App\Filament\Widgets;

use App\Models\Suggestion;
use Filament\Widgets\ChartWidget;

class SuggestionsChart extends ChartWidget
{
    protected static ?string $heading = 'Suggestions by Category';

    protected static ?int $sort = 4;

    protected function getData(): array
    {
        $categories = Suggestion::getCategoryOptions();
        $labels = [];
        $data = [];
        $colors = [
            'feature' => 'rgb(201, 162, 39)',
            'bug' => 'rgb(239, 68, 68)',
            'improvement' => 'rgb(34, 197, 94)',
            'other' => 'rgb(156, 163, 175)',
        ];

        foreach ($categories as $key => $label) {
            $labels[] = $label;
            $data[] = Suggestion::where('category', $key)->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Suggestions',
                    'data' => $data,
                    'backgroundColor' => array_values($colors),
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
