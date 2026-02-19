<?php

namespace App\Filament\App\Resources\Players\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;

class PlayersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
            \Filament\Tables\Columns\TextColumn::make('name')
            ->searchable(),
            \Filament\Tables\Columns\TextColumn::make('last_name')
            ->searchable(),
            \Filament\Tables\Columns\TextColumn::make('number')
            ->sortable(),
            \Filament\Tables\Columns\TextColumn::make('position')
            ->searchable(),
            \Filament\Tables\Columns\TextColumn::make('team.name')
            ->sortable(),
            \Filament\Tables\Columns\TextColumn::make('currentStats.avg')
            ->label('AVG')
            ->default('.000')
            ->sortable(),
        ])
            ->filters([
            //
        ])
            ->recordActions([
            EditAction::make(),
        ])
            ->toolbarActions([
            BulkActionGroup::make([
                DeleteBulkAction::make(),
            ]),
        ]);
    }
}