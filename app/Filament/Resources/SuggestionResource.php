<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SuggestionResource\Pages;
use App\Models\Suggestion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;

class SuggestionResource extends Resource
{
    protected static ?string $model = Suggestion::class;

    protected static ?string $navigationIcon = 'heroicon-o-light-bulb';

    protected static ?string $navigationLabel = 'الاقتراحات';

    protected static ?string $modelLabel = 'اقتراح';

    protected static ?string $pluralModelLabel = 'الاقتراحات';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('محتوى الاقتراح')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('العنوان')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->label('التفاصيل')
                            ->required()
                            ->columnSpanFull()
                            ->rows(5),
                    ]),
                Forms\Components\Section::make('معلومات إضافية')
                    ->schema([
                        Forms\Components\TextInput::make('author_name')
                            ->label('اسم المقترح')
                            ->maxLength(255),
                        Forms\Components\Select::make('category')
                            ->label('التصنيف')
                            ->options(Suggestion::getCategoryOptions())
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->label('الحالة')
                            ->options(Suggestion::getStatusOptions())
                            ->required(),
                        Forms\Components\TextInput::make('votes_count')
                            ->label('عدد الأصوات')
                            ->numeric()
                            ->default(0)
                            ->disabled(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('العنوان')
                    ->searchable()
                    ->limit(40)
                    ->tooltip(function ($record): string {
                        return $record->title;
                    }),
                Tables\Columns\TextColumn::make('description')
                    ->label('التفاصيل')
                    ->limit(50)
                    ->tooltip(function ($record): string {
                        return $record->description;
                    }),
                Tables\Columns\TextColumn::make('author_name')
                    ->label('المقترح')
                    ->searchable()
                    ->default('مجهول'),
                Tables\Columns\BadgeColumn::make('category')
                    ->label('التصنيف')
                    ->colors([
                        'primary' => 'feature',
                        'danger' => 'bug',
                        'warning' => 'improvement',
                        'gray' => 'other',
                    ])
                    ->formatStateUsing(fn(string $state): string => Suggestion::getCategoryOptions()[$state] ?? $state),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('الحالة')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ])
                    ->formatStateUsing(fn(string $state): string => Suggestion::getStatusOptions()[$state] ?? $state),
                Tables\Columns\TextColumn::make('votes_count')
                    ->label('الأصوات')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('التاريخ')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('الحالة')
                    ->options(Suggestion::getStatusOptions()),
                Tables\Filters\SelectFilter::make('category')
                    ->label('التصنيف')
                    ->options(Suggestion::getCategoryOptions()),
            ])
            ->actions([
                Action::make('approve')
                    ->label('قبول')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn(Suggestion $record): bool => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->modalHeading('قبول الاقتراح')
                    ->modalDescription('هل أنت متأكد من قبول هذا الاقتراح؟')
                    ->action(function (Suggestion $record): void {
                        $record->approve();
                        Notification::make()
                            ->title('تم قبول الاقتراح')
                            ->success()
                            ->send();
                    }),
                Action::make('reject')
                    ->label('رفض')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn(Suggestion $record): bool => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->modalHeading('رفض الاقتراح')
                    ->modalDescription('هل أنت متأكد من رفض هذا الاقتراح؟')
                    ->action(function (Suggestion $record): void {
                        $record->reject();
                        Notification::make()
                            ->title('تم رفض الاقتراح')
                            ->warning()
                            ->send();
                    }),
                Tables\Actions\ViewAction::make()
                    ->label('عرض'),
                Tables\Actions\EditAction::make()
                    ->label('تعديل'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('حذف المحدد'),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSuggestions::route('/'),
            'create' => Pages\CreateSuggestion::route('/create'),
            'edit' => Pages\EditSuggestion::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}
