<?php

namespace App\Filament\Resources\Payments\Schemas;

use Filament\Schemas\Schema;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\ToggleButtons;

class PaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)->schema([
                    Select::make('user_id')
                        ->relationship('user', 'name')
                        ->searchable()
                        ->required()
                        ->label('Customer (User)'),

                    Select::make('plan_id')
                        ->relationship('plan', 'name')
                        ->required()
                        ->label('Subscription Plan'),

                    Select::make('payment_method')
                        ->options([
                            'pago_movil' => 'Pago Móvil (VE)',
                            'bs_transfer' => 'Transferencia Nacional',
                            'zelle' => 'Zelle',
                            'cash' => 'Efectivo',
                        ])
                        ->required(),
                ]),

                Grid::make(3)->schema([
                    TextInput::make('reference_number')->label('Reference ID / Code')->prefix('#'),
                    TextInput::make('amount')->numeric()->prefix('$')->required(),
                    Select::make('currency')->options(['USD' => 'USD ($)', 'VES' => 'VES (Bs.)'])->default('USD')->required(),
                ]),

                Grid::make(2)->schema([
                    FileUpload::make('receipt_path')
                        ->label('Payment Receipt / Capture')
                        ->directory('receipts')
                        ->image()
                        ->maxSize(5120) // 5MB
                        ->columnSpan(1),

                    Grid::make(1)->schema([
                        ToggleButtons::make('status')
                            ->options([
                                'pending' => 'Pending Verification',
                                'approved' => 'Approved & Processed',
                                'rejected' => 'Rejected',
                            ])
                            ->colors([
                                'pending' => 'warning',
                                'approved' => 'success',
                                'rejected' => 'danger',
                            ])
                            ->inline()
                            ->default('pending')
                            ->required(),
                        Textarea::make('notes')->label('Admin Notes')->rows(3),
                    ])->columnSpan(1),
                ]),
            ]);
    }
}
