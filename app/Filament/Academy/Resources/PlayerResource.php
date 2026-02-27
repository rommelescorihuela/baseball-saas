<?php

namespace App\Filament\Academy\Resources;

use App\Filament\Academy\Resources\Players\Pages\CreatePlayer;
use App\Filament\Academy\Resources\Players\Pages\EditPlayer;
use App\Filament\Academy\Resources\Players\Pages\ListPlayers;
use App\Models\Player;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;

class PlayerResource extends Resource
{
    protected static ?string $model = Player::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Alumnos';

    protected static ?string $modelLabel = 'Alumno';

    protected static ?string $pluralModelLabel = 'Alumnos';

    protected static ?string $tenantOwnershipRelationshipName = 'team';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Datos del Alumno')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('last_name')
                            ->label('Apellido')
                            ->maxLength(255),
                        TextInput::make('number')
                            ->label('Número')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(99),
                        DatePicker::make('date_of_birth')
                            ->label('Fecha de Nacimiento')
                            ->maxDate(now())
                            ->displayFormat('d/m/Y'),
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
                            ->searchable(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('full_name')
                    ->label('Nombre Completo')
                    ->state(fn($record) => "{$record->name} {$record->last_name}")
                    ->searchable(['name', 'last_name']),
                TextColumn::make('number')
                    ->label('#')
                    ->sortable(),
                TextColumn::make('position')
                    ->label('Posición')
                    ->badge(),
                TextColumn::make('date_of_birth')
                    ->label('Fecha Nac.')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPlayers::route('/'),
            'create' => CreatePlayer::route('/create'),
            'edit' => EditPlayer::route('/{record}/edit'),
        ];
    }
}
