<?php

namespace App\Filament\Resources\Teams\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TeamForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('league_id')
                    ->relationship('league', 'name')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                Select::make('category_id')
                    ->relationship('category', 'name')
                    ->required(),
                TextInput::make('subdomain')
                    ->required(),
                \Filament\Forms\Components\FileUpload::make('logo')
                    ->image()
                    ->directory('team-logos')
                    ->visibility('public'),
                \Filament\Forms\Components\ColorPicker::make('primary_color')
                    ->default('#0f172a'),
            ]);
    }
}
