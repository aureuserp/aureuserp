<?php

namespace Webkul\Sale\Filament\Clusters\Configuration\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Webkul\Sale\Filament\Clusters\Configuration;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\OrderTemplateProductResource\Pages\CreateOrderTemplateProduct;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\OrderTemplateProductResource\Pages\EditOrderTemplateProduct;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\OrderTemplateProductResource\Pages\ListOrderTemplateProducts;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\OrderTemplateProductResource\Pages\ViewOrderTemplateProduct;
use Webkul\Sale\Models\OrderTemplateProduct;

class OrderTemplateProductResource extends Resource
{
    protected static ?string $model = OrderTemplateProduct::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-path-rounded-square';

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

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Select::make('orderTemplate.name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label(__('sales::filament/clusters/configurations/resources/order-template.form.fields.order-template')),
                        Select::make('company.name')
                            ->searchable()
                            ->preload()
                            ->label(__('sales::filament/clusters/configurations/resources/order-template.form.fields.company')),
                        Select::make('product.name')
                            ->searchable()
                            ->preload()
                            ->label(__('sales::filament/clusters/configurations/resources/order-template.form.fields.product')),
                        Select::make('uom.name')
                            ->searchable()
                            ->preload()
                            ->label(__('sales::filament/clusters/configurations/resources/order-template.form.fields.product-uom')),
                        Hidden::make('creator_id')
                            ->default(Auth::user()->id),
                        TextInput::make('display_type')
                            ->label(__('sales::filament/clusters/configurations/resources/order-template.form.fields.display-type'))
                            ->maxLength(255),
                        TextInput::make('name')
                            ->label(__('sales::filament/clusters/configurations/resources/order-template.form.fields.name'))
                            ->maxLength(255),
                        TextInput::make('quantity')
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
                TextColumn::make('sort')
                    ->label(__('sales::filament/clusters/configurations/resources/order-template.table.columns.sort'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('orderTemplate.name')
                    ->label(__('sales::filament/clusters/configurations/resources/order-template.table.columns.order-template'))
                    ->sortable(),
                TextColumn::make('company.name')
                    ->label(__('sales::filament/clusters/configurations/resources/order-template.table.columns.company'))
                    ->sortable(),
                TextColumn::make('product.name')
                    ->label(__('sales::filament/clusters/configurations/resources/order-template.table.columns.product'))
                    ->sortable(),
                TextColumn::make('uom.name')
                    ->label(__('sales::filament/clusters/configurations/resources/order-template.table.columns.product-uom'))
                    ->sortable(),
                TextColumn::make('createdBy.name')
                    ->label(__('sales::filament/clusters/configurations/resources/order-template.table.columns.created-by'))
                    ->sortable(),
                TextColumn::make('display_type')
                    ->label(__('sales::filament/clusters/configurations/resources/order-template.table.columns.display-type'))
                    ->searchable(),
                TextColumn::make('name')
                    ->label(__('sales::filament/clusters/configurations/resources/order-template.table.columns.name'))
                    ->searchable(),
                TextColumn::make('quantity')
                    ->label(__('sales::filament/clusters/configurations/resources/order-template.table.columns.quantity'))
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('sales::filament/clusters/configurations/resources/order-template.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('sales::filament/clusters/configurations/resources/order-template.table.columns.updated-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('sales::filament/clusters/configurations/resources/order-template.table.actions.delete.notification.title'))
                            ->body(__('sales::filament/clusters/configurations/resources/order-template.table.actions.delete.notification.body'))
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
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
            'index'  => ListOrderTemplateProducts::route('/'),
            'create' => CreateOrderTemplateProduct::route('/create'),
            'view'   => ViewOrderTemplateProduct::route('/{record}'),
            'edit'   => EditOrderTemplateProduct::route('/{record}/edit'),
        ];
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        TextEntry::make('sort')
                            ->label(__('sales::filament/clusters/configurations/resources/order-template.infolist.entries.sort'))
                            ->numeric(),
                        TextEntry::make('orderTemplate.name')
                            ->label(__('sales::filament/clusters/configurations/resources/order-template.infolist.entries.order-template')),
                        TextEntry::make('company.name')
                            ->label(__('sales::filament/clusters/configurations/resources/order-template.infolist.entries.company')),
                        TextEntry::make('product.name')
                            ->label(__('sales::filament/clusters/configurations/resources/order-template.infolist.entries.product')),
                        TextEntry::make('uom.name')
                            ->label(__('sales::filament/clusters/configurations/resources/order-template.infolist.entries.product-uom')),
                        TextEntry::make('display_type')
                            ->label(__('sales::filament/clusters/configurations/resources/order-template.infolist.entries.display-type')),
                        TextEntry::make('name')
                            ->label(__('sales::filament/clusters/configurations/resources/order-template.infolist.entries.name')),
                        TextEntry::make('quantity')
                            ->label(__('sales::filament/clusters/configurations/resources/order-template.infolist.entries.quantity'))
                            ->numeric(),
                    ])->columns(2),
            ]);
    }
}
