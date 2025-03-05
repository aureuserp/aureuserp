<?php

namespace Webkul\Sale\Filament\Clusters\Configuration\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Webkul\Sale\Filament\Clusters\Configuration;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\OrderTemplateProductResource\Pages;
use Webkul\Sale\Models\OrderTemplateProduct;

class OrderTemplateProductResource extends Resource
{
    protected static ?string $model = OrderTemplateProduct::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path-rounded-square';

    public static function getModelLabel(): string
    {
        return __('sales::filament/clusters/configurations/resources/order-template.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('sales::filament/clusters/configurations/resources/order-template.navigation.title');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('sales::filament/clusters/configurations/resources/order-template.navigation.group');
    }

    protected static ?string $cluster = Configuration::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('sort')
                            ->label(__('sales::filament/clusters/configurations/resources/order-template.form.fields.sort'))
                            ->numeric(),
                        Forms\Components\Select::make('orderTemplate.name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label(__('sales::filament/clusters/configurations/resources/order-template.form.fields.order-template')),
                        Forms\Components\Select::make('company.name')
                            ->searchable()
                            ->preload()
                            ->label(__('sales::filament/clusters/configurations/resources/order-template.form.fields.company')),
                        Forms\Components\Select::make('product.name')
                            ->searchable()
                            ->preload()
                            ->label(__('sales::filament/clusters/configurations/resources/order-template.form.fields.product')),
                        Forms\Components\Select::make('uom.name')
                            ->searchable()
                            ->preload()
                            ->label(__('sales::filament/clusters/configurations/resources/order-template.form.fields.product-uom')),
                        Forms\Components\Hidden::make('creator_id')
                            ->default(Auth::user()->id),
                        Forms\Components\TextInput::make('display_type')
                            ->label(__('sales::filament/clusters/configurations/resources/order-template.form.fields.display-type'))
                            ->maxLength(255),
                        Forms\Components\TextInput::make('name')
                            ->label(__('sales::filament/clusters/configurations/resources/order-template.form.fields.name'))
                            ->maxLength(255),
                        Forms\Components\TextInput::make('quantity')
                            ->label(__('sales::filament/clusters/configurations/resources/order-template.form.fields.quantity'))
                            ->required()
                            ->numeric(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sort')
                    ->label(__('sales::filament/clusters/configurations/resources/order-template.table.columns.sort'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('orderTemplate.name')
                    ->label(__('sales::filament/clusters/configurations/resources/order-template.table.columns.order-template'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('company.name')
                    ->label(__('sales::filament/clusters/configurations/resources/order-template.table.columns.company'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('product.name')
                    ->numeric()
                    ->label(__('sales::filament/clusters/configurations/resources/order-template.table.columns.product'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('uom.name')
                    ->label(__('sales::filament/clusters/configurations/resources/order-template.table.columns.product-uom'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('createdBy.name')
                    ->label(__('sales::filament/clusters/configurations/resources/order-template.table.columns.created-by'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('display_type')
                    ->label(__('sales::filament/clusters/configurations/resources/order-template.table.columns.display-type'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('sales::filament/clusters/configurations/resources/order-template.table.columns.name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->label(__('sales::filament/clusters/configurations/resources/order-template.table.columns.quantity'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('sales::filament/clusters/configurations/resources/order-template.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('sales::filament/clusters/configurations/resources/order-template.table.columns.updated-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('sales::filament/clusters/configurations/resources/order-template.table.actions.delete.notification.title'))
                            ->body(__('sales::filament/clusters/configurations/resources/order-template.table.actions.delete.notification.body'))
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('sales::filament/clusters/configurations/resources/order-template.table.bulk-actions.delete.notification.title'))
                                ->body(__('sales::filament/clusters/configurations/resources/order-template.table.bulk-actions.delete.notification.body'))
                        ),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListOrderTemplateProducts::route('/'),
            'create' => Pages\CreateOrderTemplateProduct::route('/create'),
            'view'   => Pages\ViewOrderTemplateProduct::route('/{record}'),
            'edit'   => Pages\EditOrderTemplateProduct::route('/{record}/edit'),
        ];
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make()
                    ->schema([
                        Infolists\Components\TextEntry::make('sort')
                            ->label(__('sales::filament/clusters/configurations/resources/order-template.infolist.entries.sort'))
                            ->numeric(),
                        Infolists\Components\TextEntry::make('orderTemplate.name')
                            ->label(__('sales::filament/clusters/configurations/resources/order-template.infolist.entries.order-template')),
                        Infolists\Components\TextEntry::make('company.name')
                            ->label(__('sales::filament/clusters/configurations/resources/order-template.infolist.entries.company')),
                        Infolists\Components\TextEntry::make('product.name')
                            ->label(__('sales::filament/clusters/configurations/resources/order-template.infolist.entries.product')),
                        Infolists\Components\TextEntry::make('uom.name')
                            ->label(__('sales::filament/clusters/configurations/resources/order-template.infolist.entries.product-uom')),
                        Infolists\Components\TextEntry::make('display_type')
                            ->label(__('sales::filament/clusters/configurations/resources/order-template.infolist.entries.display-type')),
                        Infolists\Components\TextEntry::make('name')
                            ->label(__('sales::filament/clusters/configurations/resources/order-template.infolist.entries.name')),
                        Infolists\Components\TextEntry::make('quantity')
                            ->label(__('sales::filament/clusters/configurations/resources/order-template.infolist.entries.quantity'))
                            ->numeric(),
                    ])->columns(2),
            ]);
    }
}
