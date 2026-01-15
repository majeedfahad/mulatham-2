<?php

namespace App\Filament\Widgets;

use App\Models\Suggestion;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class TopSuggestionsTable extends BaseWidget
{
    protected static ?string $heading = 'Top Voted Suggestions';

    protected static ?int $sort = 6;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Suggestion::query()
                    ->approved()
                    ->orderByVotes()
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Title')
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\TextColumn::make('category')
                    ->label('Category')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Suggestion::getCategoryOptions()[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        'feature' => 'warning',
                        'bug' => 'danger',
                        'improvement' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('votes_count')
                    ->label('Votes')
                    ->badge()
                    ->color('primary')
                    ->sortable(),
                Tables\Columns\TextColumn::make('author_name')
                    ->label('Author')
                    ->limit(20),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Submitted')
                    ->dateTime('M d')
                    ->sortable(),
            ])
            ->defaultSort('votes_count', 'desc')
            ->paginated(false);
    }
}
