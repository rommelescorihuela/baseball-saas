<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\GameResource\Pages;
use App\Models\Game;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class GameResource extends Resource
{
    protected static ?string $model = Game::class;

    protected static ?string $tenantOwnershipRelationshipName = 'league';

    // protected static string|\UnitEnum|null $navigationIcon = 'heroicon-o-calendar';

    // protected static string|\UnitEnum|null $navigationGroup = 'Competition';

    public static function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema
            ->schema([
                Forms\Components\Select::make('competition_id')
                    ->relationship('competition', 'name')
                    ->required(),
                Forms\Components\Select::make('category_id')
                    ->relationship('category', 'name')
                    ->required(),
                Forms\Components\Select::make('home_team_id')
                    ->relationship('homeTeam', 'name')
                    ->required(),
                Forms\Components\Select::make('visitor_team_id')
                    ->relationship('visitorTeam', 'name')
                    ->required(),
                Forms\Components\DateTimePicker::make('start_time')
                    ->required(),
                Forms\Components\TextInput::make('location'),
                Forms\Components\Select::make('status')
                    ->options([
                        'scheduled' => 'Scheduled',
                        'in_progress' => 'In Progress',
                        'finished' => 'Finished',
                        'suspended' => 'Suspended',
                        'voided' => 'Voided',
                    ])
                    ->default('scheduled')
                    ->required(),
                \Filament\Schemas\Components\Grid::make()
                    ->schema([
                        Forms\Components\TextInput::make('home_score')
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('visitor_score')
                            ->numeric()
                            ->default(0),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn(\Illuminate\Database\Eloquent\Builder $query) => $query->with(['homeTeam', 'visitorTeam', 'category']))
            ->columns([
                Tables\Columns\TextColumn::make('start_time')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('homeTeam.name')
                    ->label('Home')
                    ->searchable(),
                Tables\Columns\TextColumn::make('visitorTeam.name')
                    ->label('Visitor')
                    ->searchable(),
                Tables\Columns\TextColumn::make('location')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'scheduled' => 'gray',
                        'in_progress' => 'warning',
                        'finished' => 'success',
                        'suspended' => 'danger',
                        'voided' => 'gray',
                    }),
                Tables\Columns\TextColumn::make('score')
                    ->label('Score')
                    ->state(function (Game $record): string {
                        return "{$record->home_score} - {$record->visitor_score}";
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\Action::make('score')
                    ->label('Score Live')
                    ->icon('heroicon-o-play')
                    ->color('success')
                    ->url(fn(Game $record): string => Pages\ScoreGame::getUrl(['record' => $record])),
                \Filament\Actions\Action::make('manual_score')
                    ->label('Score Manual')
                    ->icon('heroicon-o-document-plus')
                    ->color('warning')
                    ->url(fn(Game $record): string => Pages\ManualBoxScore::getUrl(['record' => $record])),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\PlayerGameStatsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGames::route('/'),
            'create' => Pages\CreateGame::route('/create'),
            'edit' => Pages\EditGame::route('/{record}/edit'),
            'score' => Pages\ScoreGame::route('/{record}/score'),
            'manual-box-score' => Pages\ManualBoxScore::route('/{record}/manual-score'),
            'box-score' => Pages\ViewBoxScore::route('/{record}/box-score'),
        ];
    }
}