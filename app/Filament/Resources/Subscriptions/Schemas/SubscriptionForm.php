<?php

namespace App\Filament\Resources\Subscriptions\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\Grid;
use Carbon\Carbon;

class SubscriptionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)->schema([
                    Select::make('user_id')
                        ->relationship('user', 'name')
                        ->searchable()
                        ->required()
                        ->label('Customer/Manager'),

                    Select::make('plan_id')
                        ->relationship('plan', 'name')
                        ->required()
                        ->live()
                        ->afterStateUpdated(function ($state, callable $set) {
                            if ($state) {
                                $plan = \App\Models\Plan::find($state);
                                if ($plan) {
                                    $set('starts_at', now()->toDateTimeString());
                                    $set('expires_at', now()->addDays($plan->duration_days)->toDateTimeString());
                                }
                            }
                        })
                        ->label('Subscribed Plan'),
                ]),
                Grid::make(3)->schema([
                    DateTimePicker::make('starts_at')->required()->default(now()),
                    DateTimePicker::make('expires_at')->required(),
                    ToggleButtons::make('status')
                        ->options([
                            'active' => 'Active',
                            'expired' => 'Expired',
                            'pending' => 'Pending',
                        ])
                        ->colors([
                            'active' => 'success',
                            'expired' => 'danger',
                            'pending' => 'warning',
                        ])
                        ->inline()
                        ->default('active')
                        ->required(),
                ]),
            ]);
    }
}
