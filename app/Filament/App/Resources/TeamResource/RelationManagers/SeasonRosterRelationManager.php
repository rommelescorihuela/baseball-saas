<?php

namespace App\Filament\App\Resources\TeamResource\RelationManagers;

use App\Models\Player;
use App\Models\Season;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class SeasonRosterRelationManager extends RelationManager
{
    protected static string $relationship = 'players';

    protected static ?string $title = 'Roster por Temporada';

    protected static ?string $recordTitleAttribute = 'name';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('last_name')
                    ->label('Apellido')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pivot.number')
                    ->label('#')
                    ->sortable()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('pivot.position')
                    ->label('Posición')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('date_of_birth')
                    ->label('Fecha Nac.')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->label('Agregar Jugador')
                    ->modalHeading('Agregar Jugador al Roster')
                    ->modalSubmitButtonLabel('Agregar')
                    ->form(fn (Tables\Actions\AttachAction $action): array => [
                        $action->getRecordSelect()
                            ->label('Jugador')
                            ->options(function () {
                                $league = \Filament\Facades\Filament::getTenant();

                                return Player::where('league_id', $league?->id)
                                    ->get()
                                    ->mapWithKeys(fn ($player) => [
                                        $player->id => "{$player->name} {$player->last_name}",
                                    ]);
                            })
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('season_id')
                            ->label('Temporada')
                            ->options(function () {
                                $league = \Filament\Facades\Filament::getTenant();

                                return Season::where('league_id', $league?->id)
                                    ->where('is_active', true)
                                    ->pluck('name', 'id');
                            })
                            ->default(function () {
                                $league = \Filament\Facades\Filament::getTenant();

                                return Season::where('league_id', $league?->id)
                                    ->where('is_active', true)
                                    ->first()?->id;
                            })
                            ->required()
                            ->searchable(),
                        Forms\Components\TextInput::make('number')
                            ->label('Número')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(99),
                        Forms\Components\Select::make('position')
                            ->label('Posición')
                            ->options([
                                'P' => 'Pitcher (P)',
                                'C' => 'Catcher (C)',
                                '1B' => 'Primera Base (1B)',
                                '2B' => 'Segunda Base (2B)',
                                '3B' => 'Tercera Base (3B)',
                                'SS' => 'Shortstop (SS)',
                                'LF' => 'Left Field (LF)',
                                'CF' => 'Center Field (CF)',
                                'RF' => 'Right Field (RF)',
                                'DH' => 'Designated Hitter (DH)',
                                'UTIL' => 'Utility (UTIL)',
                            ])
                            ->searchable(),
                    ])
                    ->action(function (array $data, RelationManager $livewire) {
                        // AttachAction puede usar 'recordId' o el nombre del campo según configuración
                        $playerId = $data['recordId'] ?? null;

                        if ($playerId) {
                            $livewire->getOwnerRecord()->players()->attach($playerId, [
                                'season_id' => $data['season_id'],
                                'number' => $data['number'] ?? null,
                                'position' => $data['position'] ?? null,
                            ]);
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalHeading('Editar Roster')
                    ->form([
                        Forms\Components\TextInput::make('pivot.number')
                            ->label('Número')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(99),
                        Forms\Components\Select::make('pivot.position')
                            ->label('Posición')
                            ->options([
                                'P' => 'Pitcher (P)',
                                'C' => 'Catcher (C)',
                                '1B' => 'Primera Base (1B)',
                                '2B' => 'Segunda Base (2B)',
                                '3B' => 'Tercera Base (3B)',
                                'SS' => 'Shortstop (SS)',
                                'LF' => 'Left Field (LF)',
                                'CF' => 'Center Field (CF)',
                                'RF' => 'Right Field (RF)',
                                'DH' => 'Designated Hitter (DH)',
                                'UTIL' => 'Utility (UTIL)',
                            ])
                            ->searchable(),
                    ]),
                Tables\Actions\DetachAction::make()
                    ->label('Quitar'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make()
                        ->label('Quitar seleccionados'),
                ]),
            ]);
    }
}
