<?php

namespace App\Filament\App\Resources\Players\Schemas;

use App\Models\Team;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PlayerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Datos del Jugador')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(1),
                        TextInput::make('last_name')
                            ->label('Apellido')
                            ->maxLength(255)
                            ->columnSpan(1),
                        TextInput::make('number')
                            ->label('Número')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(99)
                            ->columnSpan(1),
                        DatePicker::make('date_of_birth')
                            ->label('Fecha de Nacimiento')
                            ->maxDate(now())
                            ->displayFormat('d/m/Y')
                            ->columnSpan(1),
                        Select::make('position')
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
                            ->searchable()
                            ->columnSpan(1),
                        Select::make('team_id')
                            ->label('Equipo')
                            ->options(function () {
                                $league = \Filament\Facades\Filament::getTenant();

                                return Team::where('league_id', $league?->id)
                                    ->pluck('name', 'id');
                            })
                            ->required()
                            ->searchable()
                            ->preload()
                            ->columnSpan(1),
                    ])
                    ->columns(2),
            ]);
    }
}
