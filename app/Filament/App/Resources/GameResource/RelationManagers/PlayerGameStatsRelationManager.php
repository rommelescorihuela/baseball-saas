<?php

namespace App\Filament\App\Resources\GameResource\RelationManagers;

use App\Models\PlayerGameStat;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Illuminate\Database\Eloquent\Builder;

class PlayerGameStatsRelationManager extends RelationManager
{
    protected static string $relationship = 'playerGameStats';

    protected static ?string $title = 'Estadísticas de Jugadores';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('player_id')
                    ->relationship('player', 'name', fn(Builder $query) => $query->where('league_id', $this->getOwnerRecord()->league_id))
                    ->searchable()
                    ->required(),

                Select::make('team_id')
                    ->options([
                        $this->getOwnerRecord()->home_team_id => $this->getOwnerRecord()->homeTeam->name . ' (Home)',
                        $this->getOwnerRecord()->visitor_team_id => $this->getOwnerRecord()->visitorTeam->name . ' (Visitor)',
                    ])
                    ->required(),

                Tabs::make('Stats')
                    ->tabs([
                        Tab::make('Bateo')
                            ->schema([
                                \Filament\Schemas\Components\Section::make('Ofensiva Principal')
                                    ->schema([
                                        Grid::make(4)->schema([
                                            TextInput::make('ab')->numeric()->default(0)->label('VB')->extraInputAttributes(['class' => 'font-bold text-primary']),
                                            TextInput::make('r')->numeric()->default(0)->label('CA'),
                                            TextInput::make('h')->numeric()->default(0)->label('H')->extraInputAttributes(['class' => 'font-bold']),
                                            TextInput::make('rbi')->numeric()->default(0)->label('CI'),
                                        ]),
                                    ])->compact(),
                                \Filament\Schemas\Components\Section::make('Poder y Extra Bases')
                                    ->schema([
                                        Grid::make(4)->schema([
                                            TextInput::make('doubles')->numeric()->default(0)->label('2B'),
                                            TextInput::make('triples')->numeric()->default(0)->label('3B'),
                                            TextInput::make('hr')->numeric()->default(0)->label('HR')->extraInputAttributes(['class' => 'text-warning font-bold']),
                                        ]),
                                    ])->compact()->collapsible(),
                                \Filament\Schemas\Components\Section::make('Disciplina y Otros')
                                    ->schema([
                                        Grid::make(5)->schema([
                                            TextInput::make('bb')->numeric()->default(0)->label('BB'),
                                            TextInput::make('so')->numeric()->default(0)->label('SO'),
                                            TextInput::make('hbp')->numeric()->default(0)->label('GP'),
                                            TextInput::make('sacrifice_flies')->numeric()->default(0)->label('SF'),
                                            TextInput::make('sh')->numeric()->default(0)->label('SH'),
                                        ]),
                                    ])->compact()->collapsible(),
                            ]),
                        Tab::make('Pitcheo')
                            ->schema([
                                \Filament\Schemas\Components\Section::make('Labor Monticular')
                                    ->schema([
                                        Grid::make(4)->schema([
                                            TextInput::make('ip')->numeric()->step(0.1)->default(0)->label('IP')->extraInputAttributes(['class' => 'font-bold text-primary']),
                                            TextInput::make('p_h')->numeric()->default(0)->label('H (P)'),
                                            TextInput::make('p_r')->numeric()->default(0)->label('R (P)'),
                                            TextInput::make('p_er')->numeric()->default(0)->label('ER (CL)'),
                                            TextInput::make('p_bb')->numeric()->default(0)->label('BB (P)'),
                                            TextInput::make('p_so')->numeric()->default(0)->label('SO (P)'),
                                            TextInput::make('p_hr')->numeric()->default(0)->label('HR (P)')->extraInputAttributes(['class' => 'text-warning font-bold']),
                                        ])
                                    ])->compact(),
                            ]),
                    ])->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('player.name')
            ->columns([
                Tables\Columns\TextColumn::make('player.name')->label('Jugador')
                    ->color('primary')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('team.name')->label('Equipo')
                    ->color('gray'),
                Tables\Columns\TextColumn::make('ab')->label('VB')
                    ->alignCenter()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('h')->label('H')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('hr')->label('HR')
                    ->alignCenter()
                    ->color('warning')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('rbi')->label('CI')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('avg')->label('AVG')
                    ->alignCenter()
                    ->weight('bold')
                    ->color('success')
                    ->numeric(
                        decimalPlaces: 3,
                    ),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
