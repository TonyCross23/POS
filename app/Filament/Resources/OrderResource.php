<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Order;
use App\Models\Product;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Number;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\SelectColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\ToggleButtons;
use App\Filament\Resources\OrderResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Filament\Resources\OrderResource\Widgets\OrderState;
use App\Filament\Resources\OrderResource\RelationManagers\AddressRelationManager;

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
                    Select::make('payment_method')
                      ->required()
                      ->preload()
                      ->searchable()
                      ->options([
                        'stripe' => 'Stripe',
                        'cod' => 'Cash on delivery',
                      ]),
                    Select::make('payment_status')
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
                      ->default('new')
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
                    Select::make('currency')
                      ->required()
                      ->searchable()
                      ->preload()
                      ->options([
                        'usd' => 'USD',
                        'mmk' => 'MMK',
                        'eur' => 'EUR',
                        'thb' => 'THB'
                      ]),
                    Select::make('shipping_method')
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

            ]),
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
                     ->columnSpan(4)
                     ->reactive()
                     ->afterStateUpdated(fn($state, Set $set) => $set('unit_amount', Product::find($state)?->price ?? 0))
                     ->afterStateUpdated(fn($state, Set $set) => $set('total_amount', Product::find($state)?->price ?? 0)),
                  TextInput::make('quantity')
                     ->numeric()
                     ->minValue(1)
                     ->default(1)
                     ->columnSpan(2)
                     ->reactive()
                     ->afterStateUpdated(fn($state, Set $set, Get $get) => $set('total_amount', $state*$get('unit_amount'))),
                  TextInput::make('unit_amount')
                     ->numeric()
                     ->disabled()
                     ->dehydrated()
                     ->columnSpan(3),
                  TextInput::make('total_amount')
                     ->numeric()
                     ->columnSpan(3),
                ])->columns(12),
                Placeholder::make('grand_total_amount')
                    ->label('Total price')
                    ->content(function(Get $get, Set $set) {
                        $total = 0;
                        if(!$repeaters = $get('items')) {
                            return $total;
                        };

                        foreach ($repeaters as $key => $repeater) {
                            $total += $get("items.{$key}.total_amount");
                        };
                        $set('grand_total',$total);
                        return Number::currency($total,'USD');
                    }),
                    Hidden::make('grand_total')
                        ->default(0),
             ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Customer')
                    ->sortable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('grand_total')
                    ->label('Total price')
                    ->numeric()
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_method')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('payment_status')
                    ->sortable()
                    ->searchable(),
                SelectColumn::make('status')
                    ->searchable()
                    ->options([
                        'new' => 'New',
                        'processing' => 'Processing',
                        'shipped' => 'Shipping',
                        'delivered' => 'Delivered',
                        'cancelled' => 'Cancelled'
                    ]),
                Tables\Columns\TextColumn::make('shipping_amount')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault:true),
                Tables\Columns\TextColumn::make('shipping_method')
                    ->searchable(),
                Tables\Columns\TextColumn::make('currency')
                    ->searchable()
                    ->sortable(),
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
               ActionGroup::make([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
               ])
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
            AddressRelationManager::class,
        ];
    }

    public static function getNavigationBadge() : ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor (): string|array|null
    {
        return static::getModel()::count() > 100 ? "danger" : "success";
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
