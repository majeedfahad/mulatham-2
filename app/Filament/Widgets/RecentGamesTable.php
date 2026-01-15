<?php

namespace App\Filament\Widgets;

use App\Models\Room;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentGamesTable extends BaseWidget
{
    protected static ?string $heading = 'Recent Games';

    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(Room::query()->latest()->limit(10))
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Room Code')
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'lobby' => 'gray',
                        'playing' => 'success',
                        'finished' => 'info',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('phase')
                    ->badge()
                    ->color('warning'),
                Tables\Columns\TextColumn::make('players_count')
                    ->label('Players')
                    ->counts('players'),
                Tables\Columns\TextColumn::make('questions_count')
                    ->label('Questions')
                    ->counts('questions'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M d, H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated(false);
    }
}
