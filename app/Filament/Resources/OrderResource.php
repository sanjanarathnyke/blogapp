<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()->schema([
                    Section::make('Order Information')->schema([
                        Select::make('user_id')
                        ->label('Customer')
                        ->relationship('user','name')
                        ->searchable()
                        ->preload()
                        ->required(),

                        Select::make('payment_method')
                        ->options([
                            'card'=>'card-payment',
                            'cod'=>'cash-on-delivary'
                        ])->required(),

                    Select::make('Payment Status')
                            ->options([
                                'pending'=>'pending',
                                'paid'=>'paid',
                                'faild'=>'faild'
                            ])->default('pending')
                            ->required(),
                   
                    ToggleButtons::make('status')
                                ->inline()
                                ->required()
                                ->options([
                                    'new'=>'new',
                                    'processing'=>'processing',
                                    'shipped'=>'shipped',
                                    'delivered' => 'Delivered'
                                ])
                                ->colors([
                                    'new'=>'info',
                                    'processing'=>'info',
                                    'shipped'=>'info',
                                   'delivered' => 'info' 
                                ]),
                    Select::make('currency')
                                ->options([
                                    'lkr'=>'LK',
                                    'usd'=>'USD',
                                    'euro'=>'EURO'
                                ])->default('usd')
                                ->required(),
                    Select::make('shipping_method')
                                ->options([
                                    'fedex'=>'FedEx',
                                    'amazon' => 'Amazon',
                                    'dhl'=>'DHL',
                                    'ups'=>'UPS',
                                    'usps'=>'USPS'
                                ]),
                    Textarea::make('notes')
                    ->columnSpanFull()
                                
                    ])->columns(2),

                    Section::make('Order Items')->schema([
                        Repeater::make('items')->schema([
                            Select::make('product_id')
                            ->relationship('product','name')
                            ->dehydrated()
                        ])
                    ])

                ])->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
