<?php

namespace App\Filament\Resources\Plans\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Grid;
use Illuminate\Support\Str;

class PlanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)->schema([
                    TextInput::make('name')
                        ->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn(string $context, $state, callable $set) => $context === 'create' ? $set('slug', Str::slug($state)) : null),
                    TextInput::make('slug')->required()->unique(ignoreRecord: true),
                    TextInput::make('price')->numeric()->prefix('$')->required()->default(0.00),
                    TextInput::make('duration_days')->numeric()->label('Duration (Days)')->required()->default(30),
                    TextInput::make('max_teams')->numeric()->label('Maximum Teams')->helperText('Null = Unlimited'),
                    Toggle::make('is_active')->label('Published')->default(true)->inline(false),
                    Textarea::make('description')->columnSpanFull(),
                ]),
            ]);
    }
}
