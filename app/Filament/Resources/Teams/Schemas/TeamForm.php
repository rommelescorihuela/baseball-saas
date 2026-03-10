<?php

namespace App\Filament\Resources\Teams\Schemas;

use Filament\Schemas\Schema;

class TeamForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('Detalles del Equipo')
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('name')
                            ->label('Nombre del Equipo')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, callable $set) => $set('slug', \Illuminate\Support\Str::slug($state))),
                        \Filament\Forms\Components\TextInput::make('slug')
                            ->required()
                            ->unique(\App\Models\Team::class, 'slug', ignoreRecord: true),
                        \Filament\Forms\Components\Select::make('league_id')
                            ->label('Liga')
                            ->relationship('league', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder('Equipo Independiente (Sin Liga)'),
                        \Filament\Forms\Components\FileUpload::make('logo')
                            ->image()
                            ->directory('teams/logos')
                            ->disk('public'),
                    ])->columns(2),
            ]);
    }
}
