<?php

namespace App\Filament\Resources\PlayerStats\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PlayerStatInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('player_id')
                    ->numeric(),
                TextEntry::make('game_id')
                    ->numeric(),
                TextEntry::make('at_bats')
                    ->numeric(),
                TextEntry::make('hits')
                    ->numeric(),
                TextEntry::make('runs')
                    ->numeric(),
                TextEntry::make('home_runs')
                    ->numeric(),
                TextEntry::make('rbis')
                    ->numeric(),
                TextEntry::make('walks')
                    ->numeric(),
                TextEntry::make('strikeouts')
                    ->numeric(),
                TextEntry::make('innings_pitched')
                    ->numeric(),
                TextEntry::make('strikeouts_pitched')
                    ->numeric(),
                TextEntry::make('runs_allowed')
                    ->numeric(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
