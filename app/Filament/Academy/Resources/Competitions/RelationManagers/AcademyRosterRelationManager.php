<?php

namespace App\Filament\Academy\Resources\Competitions\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Actions\DetachAction;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class AcademyRosterRelationManager extends RelationManager
{
    protected static string $relationship = 'roster_players';

    protected static ?string $title = 'Roster del Equipo';

    protected static ?string $recordTitleAttribute = 'full_name';

    public function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema
            ->schema([
                Forms\Components\TextInput::make('number')
                    ->label('Número')
                    ->numeric(),
                Forms\Components\TextInput::make('position')
                    ->label('Posición'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Jugador')
                    ->formatStateUsing(fn($record) => "{$record->name} {$record->last_name}"),
                Tables\Columns\TextColumn::make('number')
                    ->label('#'),
                Tables\Columns\TextColumn::make('position')
                    ->label('Posición'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                \Filament\Actions\AttachAction::make()
                    ->label('Inscribir Jugador en Torneo')
                    ->form(fn(\Filament\Actions\AttachAction $action): array => [
                        $action->getRecordSelect(),
                        Forms\Components\TextInput::make('number')
                            ->label('Número para este torneo')
                            ->numeric()
                            ->required(),
                        Forms\Components\Select::make('position')
                            ->label('Posición principal')
                            ->options([
                                'P' => 'Pitcher',
                                'C' => 'Catcher',
                                'IF' => 'Infield',
                                'OF' => 'Outfield',
                            ])
                            ->required(),
                        Forms\Components\Hidden::make('team_id')
                            ->default(fn() => Filament::getTenant()?->id),
                    ])
                    ->mutateFormDataUsing(function (array $data) {
                        $data['team_id'] = Filament::getTenant()->id;
                        return $data;
                    })
                    ->preloadRecordSelect(),
            ])
            ->actions([
                \Filament\Actions\DetachAction::make()
                    ->label('Quitar del Roster'),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DetachBulkAction::make(),
                ]),
            ]);
    }
}
