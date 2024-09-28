<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Order;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\ToggleButtons;
use App\Filament\Resources\OrderResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\OrderResource\RelationManagers;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    public static function form(Form $form): Form
    {
        return $form
          ->schema([
            Section::make([
                Grid::make()
                ->schema([
                    Select::make('user_id')
                      ->label('Customer')
                      ->required()
                      ->searchable()
                      ->preload()
                      ->relationship('user','name'),
                    Select::make('Payment method')
                      ->required()
                      ->preload()
                      ->searchable()
                      ->options([
                        'stripe' => 'Stripe',
                        'cod' => 'Cash on delivery',
                      ]),
                    Select::make('Payment status')
                      ->required()
                      ->searchable()
                      ->preload()
                      ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'failed' => 'Failed'
                      ]),
                    ToggleButtons::make('status')
                      ->required()
                      ->inline()
                      ->options([
                        'new' => 'New',
                        'processing' => 'Processing',
                        'shipped' => 'Shipping',
                        'delivered' => 'Delivered',
                        'cancelled' => 'Cancelled'
                      ])
                      ->colors([
                        'new' => 'info',
                        'processing' => 'warning',
                        'shipped' => 'success',
                        'delivered' => 'success',
                        'cancelled' => 'danger'
                      ])
                      ->icons([
                        'new' => 'heroicon-m-sparkles',
                        'processing' => 'heroicon-m-arrow-path',
                        'shipped' => 'heroicon-m-truck',
                        'delivered' => 'heroicon-m-check-badge',
                        'cancelled' => 'heroicon-m-x-circle'
                      ]),
                    Select::make('Currency')
                      ->required()
                      ->searchable()
                      ->preload()
                      ->options([
                        'usd' => 'USD',
                        'mmk' => 'MMK',
                        'eur' => 'EUR',
                        'thb' => 'THB'
                      ]),
                    Select::make('Shipping method')
                      ->searchable()
                      ->preload()
                      ->options([
                        'fedex' => 'FedEx',
                        'ups' => 'UPS',
                        'rhl' => 'Royal',
                        'usps' => 'USPS'
                      ]),
                    Textarea::make('notes')
                      ->columnSpanFull(),
                ])->columnSpan(2),
            Section::make('Order items')->schema([
                Repeater::make('items')
                ->relationship()
                ->schema([
                  Select::make('product_id')
                     ->required()
                     ->searchable()
                     ->preload()
                     ->distinct()
                     ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                     ->relationship('product','name')
                     ->columnSpan(4),
                  TextInput::make('qualtity')
                     ->numeric()
                     ->dehydrated()
                     ->minValue(1)
                     ->default(1)
                     ->columnSpan(2),
                  TextInput::make('unit_amount')
                     ->numeric()
                     ->disabled()
                     ->columnSpan(3),
                  TextInput::make('unit_amount')
                     ->numeric()
                     ->columnSpan(3),
                ])->columns(12)
             ])
            ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('grand_total')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_method')
                    ->searchable(),
                Tables\Columns\TextColumn::make('payment_status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\TextColumn::make('currency')
                    ->searchable(),
                Tables\Columns\TextColumn::make('shipping_amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('shipping_method')
                    ->searchable(),
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
