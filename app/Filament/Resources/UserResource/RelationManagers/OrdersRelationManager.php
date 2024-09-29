<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use App\Models\Order;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\ActionGroup;
use App\Filament\Resources\OrderResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class OrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'orders';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
            //
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
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
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->actions([
                ActionGroup::make([
                    Action::make('View Order')
                        ->url(fn(Order $record):string => OrderResource::getUrl('view',['record' => $record]))
                        ->color('info')
                        ->icon('heroicon-o-eye'),
                    Tables\Actions\DeleteAction::make(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
