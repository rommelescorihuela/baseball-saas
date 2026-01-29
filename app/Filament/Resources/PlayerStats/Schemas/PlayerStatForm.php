<?php

namespace App\Filament\Resources\PlayerStats\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PlayerStatForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('player_id')
                    ->required()
                    ->numeric(),
                TextInput::make('game_id')
                    ->required()
                    ->numeric(),
                TextInput::make('at_bats')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('hits')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('runs')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('home_runs')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('rbis')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('walks')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('strikeouts')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('innings_pitched')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('strikeouts_pitched')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('runs_allowed')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
