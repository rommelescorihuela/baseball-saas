<?php

namespace App\Filament\Resources\PlayerStats\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PlayerStatsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('player_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('game_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('at_bats')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('hits')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('runs')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('home_runs')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('rbis')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('walks')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('strikeouts')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('innings_pitched')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('strikeouts_pitched')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('runs_allowed')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
