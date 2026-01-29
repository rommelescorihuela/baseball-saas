<?php

namespace App\Filament\Resources\Games\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class GameInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('season_id')
                    ->numeric(),
                TextEntry::make('home_team_id')
                    ->numeric(),
                TextEntry::make('away_team_id')
                    ->numeric(),
                TextEntry::make('game_date')
                    ->dateTime(),
                TextEntry::make('home_score')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('away_score')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
