<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use App\Models\Order;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Resources\OrderResource;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestOrders extends BaseWidget
{
    protected int | array | string $columnSpan = 'full';
    protected static ?int $sort = 2;

    public function table(Table $table): Table
    {
        return $table
            ->query(OrderResource::getEloquentQuery())
            ->defaultPaginationPageOption(5)
            ->defaultSort('created_at','desc')
            ->columns([
                TextColumn::make('user.name')
                        ->label('Customer')
                        ->sortable()
                        ->searchable(),
                TextColumn::make('status')
                        ->badge()
                        ->sortable()
                        ->searchable()
                        ->color(fn(string $state):string => match($state) {
                            'new' => 'info',
                            'processing' => 'warning',
                            'shipped' => 'success',
                            'delivered' => 'success',
                            'cancelled' => 'danger',
                        })
                        ->icon(fn(string $state):string => match($state) {
                            'new' => 'heroicon-m-sparkles',
                            'processing' => 'heroicon-m-arrow-path',
                            'shipped' => 'heroicon-m-truck',
                            'delivered' => 'heroicon-m-check-badge',
                            'cancelled' => 'heroicon-m-x-circle'
                        }),
                TextColumn::make('payment_method')
                        ->sortable()
                        ->searchable(),
                TextColumn::make('payment_status')
                        ->sortable()
                        ->searchable(),
                TextColumn::make('grand_total')
                        ->label('Total price')
                        ->money('USD'),
                TextColumn::make('created_at')
                        ->label('Order date')
                        ->dateTime()
                        ->sortable(),
            ])
        ->actions([
            Action::make('View Order')
                ->url(fn(Order $record):string => OrderResource::getUrl('view', ['record' => $record]))
                ->icon('heroicon-o-eye'),
        ]);
    }
}
