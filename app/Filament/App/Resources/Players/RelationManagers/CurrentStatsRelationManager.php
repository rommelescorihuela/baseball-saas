<?php

namespace App\Filament\App\Resources\Players\RelationManagers;

use App\Models\Season;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

class CurrentStatsRelationManager extends RelationManager
{
    protected static string $relationship = 'currentStats';

    protected static ?string $title = 'Estadísticas de Temporada';

    protected static ?string $recordTitleAttribute = 'season.name';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('season_id')
                    ->label('Temporada')
                    ->options(function () {
                        $league = \Filament\Facades\Filament::getTenant();
                        return Season::where('league_id', $league?->id)
                            ->where('is_active', true)
                            ->pluck('name', 'id');
                    })
                    ->required()
                    ->disableOptionWhen(
                        fn(string $value): bool =>
                        $this->getOwnerRecord()->currentStats()->where('season_id', $value)->exists()
                    ),
                \Filament\Schemas\Components\Section::make('Bateo General')
                    ->description('Métricas ofensivas principales y contacto.')
                    ->icon('heroicon-o-chart-bar')
                    ->schema([
                        Grid::make(4)->schema([
                            TextInput::make('games_played')->label('JJ')->numeric()->default(0)->extraInputAttributes(['class' => 'font-bold text-primary']),
                            TextInput::make('at_bats')->label('VB')->numeric()->default(0),
                            TextInput::make('runs')->label('CA')->numeric()->default(0),
                            TextInput::make('hits')->label('H')->numeric()->default(0)->extraInputAttributes(['class' => 'font-bold']),
                        ])
                    ])->collapsible()->compact(),
                \Filament\Schemas\Components\Section::make('Poder y Producción')
                    ->description('Extra bases y carreras impulsadas.')
                    ->icon('heroicon-o-fire')
                    ->schema([
                        Grid::make(4)->schema([
                            TextInput::make('doubles')->label('2B')->numeric()->default(0),
                            TextInput::make('triples')->label('3B')->numeric()->default(0),
                            TextInput::make('home_runs')->label('HR')->numeric()->default(0)->extraInputAttributes(['class' => 'text-warning font-bold']),
                            TextInput::make('runs_batted_in')->label('CI')->numeric()->default(0),
                        ])
                    ])->collapsible()->compact(),
                \Filament\Schemas\Components\Section::make('Disciplina y Velocidad')
                    ->icon('heroicon-o-bolt')
                    ->schema([
                        Grid::make(4)->schema([
                            TextInput::make('walks')->label('BB')->numeric()->default(0),
                            TextInput::make('strikeouts')->label('K')->numeric()->default(0),
                            TextInput::make('stolen_bases')->label('BR')->numeric()->default(0),
                            TextInput::make('caught_stealing')->label('AR')->numeric()->default(0),
                        ])
                    ])->collapsible()->compact(),
                \Filament\Schemas\Components\Section::make('Métricas de Pitcheo')
                    ->icon('heroicon-o-play')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('innings_pitched')->label('IP (Pitcher)')->numeric()->step('0.1')->default(0),
                            TextInput::make('earned_runs')->label('CL (Pitcher)')->numeric()->default(0),
                        ])
                    ])->collapsible()->compact(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('season.name')
            ->columns([
                Tables\Columns\TextColumn::make('season.name')
                    ->label('Temporada')
                    ->searchable()
                    ->sortable()
                    ->color('primary')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('games_played')
                    ->label('JJ')
                    ->alignCenter()
                    ->color('gray')
                    ->sortable(),
                Tables\Columns\TextColumn::make('at_bats')
                    ->label('VB')
                    ->alignCenter()
                    ->color('gray')
                    ->sortable(),
                Tables\Columns\TextColumn::make('runs')
                    ->label('C')
                    ->alignCenter()
                    ->sortable(),
                Tables\Columns\TextColumn::make('hits')
                    ->label('H')
                    ->alignCenter()
                    ->sortable(),
                Tables\Columns\TextColumn::make('doubles')
                    ->label('2B')
                    ->alignCenter()
                    ->color('gray')
                    ->sortable(),
                Tables\Columns\TextColumn::make('triples')
                    ->label('3B')
                    ->alignCenter()
                    ->color('gray')
                    ->sortable(),
                Tables\Columns\TextColumn::make('home_runs')
                    ->label('HR')
                    ->alignCenter()
                    ->color('warning')
                    ->weight('bold')
                    ->sortable(),
                Tables\Columns\TextColumn::make('runs_batted_in')
                    ->label('CI')
                    ->alignCenter()
                    ->sortable(),
                Tables\Columns\TextColumn::make('walks')
                    ->label('BB')
                    ->alignCenter()
                    ->color('gray')
                    ->sortable(),
                Tables\Columns\TextColumn::make('strikeouts')
                    ->label('K')
                    ->alignCenter()
                    ->color('gray')
                    ->sortable(),
                Tables\Columns\TextColumn::make('avg')
                    ->label('AVG')
                    ->alignCenter()
                    ->weight('bold')
                    ->color('success')
                    ->numeric(
                        decimalPlaces: 3,
                    )
                    ->sortable(),
                Tables\Columns\TextColumn::make('obp')
                    ->label('OBP')
                    ->alignCenter()
                    ->color('info')
                    ->numeric(
                        decimalPlaces: 3,
                    )
                    ->sortable(),
                Tables\Columns\TextColumn::make('slg')
                    ->label('SLG')
                    ->alignCenter()
                    ->color('info')
                    ->numeric(
                        decimalPlaces: 3,
                    )
                    ->sortable(),
                Tables\Columns\TextColumn::make('ops')
                    ->label('OPS')
                    ->alignCenter()
                    ->weight('bold')
                    ->color('primary')
                    ->numeric(
                        decimalPlaces: 3,
                    )
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Agregar Estadísticas')
                    ->modalHeading('Añadir Estadísticas Manuales')
                    ->mutateFormDataUsing(function (array $data): array {
                        // Calculate derived stats
                        $ab = (int) ($data['at_bats'] ?? 0);
                        $h = (int) ($data['hits'] ?? 0);
                        $doubles = (int) ($data['doubles'] ?? 0);
                        $triples = (int) ($data['triples'] ?? 0);
                        $hr = (int) ($data['home_runs'] ?? 0);
                        $bb = (int) ($data['walks'] ?? 0);

                        $avg = $ab > 0 ? $h / $ab : 0;
                        $obp = ($ab + $bb) > 0 ? ($h + $bb) / ($ab + $bb) : 0;

                        $tb = ($h - $doubles - $triples - $hr) + (2 * $doubles) + (3 * $triples) + (4 * $hr);
                        $slg = $ab > 0 ? $tb / $ab : 0;
                        $ops = $obp + $slg;

                        $data['avg'] = number_format($avg, 3, '.', '');
                        $data['obp'] = number_format($obp, 3, '.', '');
                        $data['slg'] = number_format($slg, 3, '.', '');
                        $data['ops'] = number_format($ops, 3, '.', '');

                        // ERA Calculation (IP format is x.1, x.2 for outs)
                        $ip = (float) ($data['innings_pitched'] ?? 0);
                        $er = (int) ($data['earned_runs'] ?? 0);

                        $fullInnings = floor($ip);
                        $outs = ($ip - $fullInnings) * 10;
                        $totalOuts = ($fullInnings * 3) + $outs;

                        $era = $totalOuts > 0 ? ($er * 27) / $totalOuts : 0;
                        $data['era'] = number_format($era, 2, '.', '');

                        return $data;
                    }),
            ])
            ->actions([
                EditAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        // Recalculate on edit
                        $ab = (int) ($data['at_bats'] ?? 0);
                        $h = (int) ($data['hits'] ?? 0);
                        $doubles = (int) ($data['doubles'] ?? 0);
                        $triples = (int) ($data['triples'] ?? 0);
                        $hr = (int) ($data['home_runs'] ?? 0);
                        $bb = (int) ($data['walks'] ?? 0);

                        $avg = $ab > 0 ? $h / $ab : 0;
                        $obp = ($ab + $bb) > 0 ? ($h + $bb) / ($ab + $bb) : 0;

                        $tb = ($h - $doubles - $triples - $hr) + (2 * $doubles) + (3 * $triples) + (4 * $hr);
                        $slg = $ab > 0 ? $tb / $ab : 0;
                        $ops = $obp + $slg;

                        $data['avg'] = number_format($avg, 3, '.', '');
                        $data['obp'] = number_format($obp, 3, '.', '');
                        $data['slg'] = number_format($slg, 3, '.', '');
                        $data['ops'] = number_format($ops, 3, '.', '');

                        $ip = (float) ($data['innings_pitched'] ?? 0);
                        $er = (int) ($data['earned_runs'] ?? 0);
                        $fullInnings = floor($ip);
                        $outs = ($ip - $fullInnings) * 10;
                        $totalOuts = ($fullInnings * 3) + $outs;

                        $era = $totalOuts > 0 ? ($er * 27) / $totalOuts : 0;
                        $data['era'] = number_format($era, 2, '.', '');

                        return $data;
                    }),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('season_id', 'desc');
    }
}
