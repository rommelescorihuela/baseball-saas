<?php

namespace App\Filament\App\Resources\Players\Tables;

use App\Models\Team;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PlayersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('last_name')
                    ->label('Apellido')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('number')
                    ->label('#')
                    ->sortable()
                    ->alignCenter(),
                TextColumn::make('position')
                    ->label('Posición')
                    ->searchable()
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'P' => 'warning',
                        'C' => 'info',
                        default => 'gray',
                    }),
                TextColumn::make('team.name')
                    ->label('Equipo')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('date_of_birth')
                    ->label('Edad')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('currentStats.avg')
                    ->label('AVG')
                    ->default('.000')
                    ->sortable()
                    ->alignCenter(),
                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('creator.name')
                    ->label('Registrado por')
                    ->sortable()
                    ->toggleable()
                    ->placeholder('Sistema'),
            ])
            ->filters([
                SelectFilter::make('team_id')
                    ->label('Equipo')
                    ->options(function () {
                        $league = \Filament\Facades\Filament::getTenant();

                        return Team::where('league_id', $league?->id)
                            ->pluck('name', 'id');
                    })
                    ->searchable()
                    ->preload(),
                SelectFilter::make('position')
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
                    ]),
            ])
            ->recordActions([
                \Filament\Actions\ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
