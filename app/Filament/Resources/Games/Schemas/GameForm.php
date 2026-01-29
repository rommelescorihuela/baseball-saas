<?php

namespace App\Filament\Resources\Games\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class GameForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('season_id')
                    ->required()
                    ->numeric(),
                TextInput::make('home_team_id')
                    ->required()
                    ->numeric(),
                TextInput::make('away_team_id')
                    ->required()
                    ->numeric(),
                DateTimePicker::make('game_date')
                    ->required(),
                TextInput::make('home_score')
                    ->numeric(),
                TextInput::make('away_score')
                    ->numeric(),
            ]);
    }
}
