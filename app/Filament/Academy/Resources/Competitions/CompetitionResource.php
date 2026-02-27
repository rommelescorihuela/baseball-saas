<?php

namespace App\Filament\Academy\Resources\Competitions;

use App\Filament\Academy\Resources\Competitions\Pages;
use App\Models\Competition;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Builder;

class CompetitionResource extends Resource
{
    protected static ?string $model = Competition::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'Mis Competencias';

    protected static ?string $modelLabel = 'Competencia';

    protected static ?string $pluralModelLabel = 'Mis Competencias';

    protected static bool $isScopedToTenant = false;

    public static function getEloquentQuery(): Builder
    {
        $team = Filament::getTenant();

        return parent::getEloquentQuery()
            ->whereHas('category.teams', function (Builder $query) use ($team) {
                $query->where('teams.id', $team->id)
                    ->where('category_team.status', 'approved');
            });
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('season.league.name')
                    ->label('Liga')
                    ->sortable(),
                TextColumn::make('season.name')
                    ->label('Temporada')
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Torneo / Competencia')
                    ->searchable(),
                TextColumn::make('category.name')
                    ->label('Categoría'),
            ])
            ->filters([
                //
            ])
            ->actions([
                \Filament\Actions\ViewAction::make()
                    ->label('Gestionar Roster'),
            ])
            ->bulkActions([
                //
            ]);
    }

    public static function getRelations(): array
    {
        return [
            \App\Filament\Academy\Resources\Competitions\RelationManagers\AcademyRosterRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCompetitions::route('/'),
            'view' => Pages\ViewCompetition::route('/{record}'),
        ];
    }
}
