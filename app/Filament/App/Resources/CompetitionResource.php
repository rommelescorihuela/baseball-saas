<?php

namespace App\Filament\App\Resources;

use App\Models\Competition;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CompetitionResource extends Resource
{
    protected static ?string $model = Competition::class;

    protected static ?string $tenantOwnershipRelationshipName = 'league';

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-trophy';

    public static function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema
            ->schema([
                \Filament\Forms\Components\Select::make('season_id')
                    ->relationship('season', 'name')
                    ->required(),
                \Filament\Forms\Components\Select::make('category_id')
                    ->relationship('category', 'name')
                    ->required(),
                \Filament\Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                \Filament\Forms\Components\DatePicker::make('start_date')
                    ->required(),
                \Filament\Forms\Components\DatePicker::make('end_date')
                    ->required(),
                \Filament\Forms\Components\Toggle::make('is_active')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('season.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\App\Resources\CompetitionResource\Pages\ListCompetitions::route('/'),
            'create' => \App\Filament\App\Resources\CompetitionResource\Pages\CreateCompetition::route('/create'),
            'edit' => \App\Filament\App\Resources\CompetitionResource\Pages\EditCompetition::route('/{record}/edit'),
        ];
    }

}