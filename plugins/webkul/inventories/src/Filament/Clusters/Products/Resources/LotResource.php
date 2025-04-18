<?php

namespace Webkul\Inventory\Filament\Clusters\Products\Resources;

use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Webkul\Inventory\Enums;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\DeliveryResource\Pages\EditDelivery;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\DropshipResource\Pages\EditDropship;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\InternalResource\Pages\EditInternal;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\ReceiptResource\Pages\EditReceipt;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\ScrapResource\Pages\CreateScrap;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\ScrapResource\Pages\EditScrap;
use Webkul\Inventory\Filament\Clusters\Products;
use Webkul\Inventory\Filament\Clusters\Products\Resources\LotResource\Pages;
use Webkul\Inventory\Filament\Clusters\Products\Resources\ProductResource\Pages\ManageQuantities;
use Webkul\Inventory\Models\Lot;
use Webkul\Inventory\Settings\TraceabilitySettings;

class LotResource extends Resource
{
    protected static ?string $model = Lot::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = Products::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 3;

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function isDiscovered(): bool
    {
        if (app()->runningInConsole()) {
            return true;
        }

        return app(TraceabilitySettings::class)->enable_lots_serial_numbers;
    }

    public static function getNavigationLabel(): string
    {
        return __('inventories::filament/clusters/products/resources/lot.navigation.title');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('inventories::filament/clusters/products/resources/lot.form.sections.general.title'))
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(__('inventories::filament/clusters/products/resources/lot.form.sections.general.fields.name'))
                            ->required()
                            ->maxLength(255)
                            ->autofocus()
                            ->placeholder(__('inventories::filament/clusters/products/resources/lot.form.sections.general.fields.name-placeholder'))
                            ->extraInputAttributes(['style' => 'font-size: 1.5rem;height: 3rem;']),
                        Forms\Components\Group::make()
                            ->schema([
                                Forms\Components\Select::make('product_id')
                                    ->label(__('inventories::filament/clusters/products/resources/lot.form.sections.general.fields.product'))
                                    ->relationship('product', 'name')
                                    ->relationship(
                                        name: 'product',
                                        titleAttribute: 'name',
                                        modifyQueryUsing: fn (Builder $query) => $query->where('tracking', Enums\ProductTracking::LOT)->whereNull('is_configurable'),
                                    )
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('inventories::filament/clusters/products/resources/lot.form.sections.general.fields.product-hint-tooltip'))
                                    ->hiddenOn([
                                        EditReceipt::class,
                                        EditDelivery::class,
                                        EditInternal::class,
                                        EditDropship::class,
                                        ManageQuantities::class,
                                        CreateScrap::class,
                                        EditScrap::class,
                                    ]),
                                Forms\Components\TextInput::make('reference')
                                    ->label(__('inventories::filament/clusters/products/resources/lot.form.sections.general.fields.reference'))
                                    ->maxLength(255)
                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('inventories::filament/clusters/products/resources/lot.form.sections.general.fields.reference-hint-tooltip')),
                                Forms\Components\RichEditor::make('description')
                                    ->label(__('inventories::filament/clusters/products/resources/lot.form.sections.general.fields.description'))
                                    ->columnSpan(2),
                            ])
                            ->columns(2),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('inventories::filament/clusters/products/resources/lot.table.columns.name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('product.name')
                    ->label(__('inventories::filament/clusters/products/resources/lot.table.columns.product'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('reference')
                    ->label(__('inventories::filament/clusters/products/resources/lot.table.columns.reference'))
                    ->placeholder('—')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_quantity')
                    ->label(__('inventories::filament/clusters/products/resources/lot.table.columns.on-hand-qty'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('inventories::filament/clusters/products/resources/lot.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('inventories::filament/clusters/products/resources/lot.table.columns.updated-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Tables\Grouping\Group::make('product.name')
                    ->label(__('inventories::filament/clusters/products/resources/lot.table.groups.product')),
                Tables\Grouping\Group::make('location.full_name')
                    ->label(__('inventories::filament/clusters/products/resources/lot.table.groups.location')),
                Tables\Grouping\Group::make('created_at')
                    ->label(__('inventories::filament/clusters/products/resources/lot.table.groups.created-at'))
                    ->date(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('product_id')
                    ->label(__('inventories::filament/clusters/products/resources/lot.table.filters.product'))
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('location_id')
                    ->label(__('inventories::filament/clusters/products/resources/lot.table.filters.location'))
                    ->relationship('location', 'full_name')
                    ->searchable()
                    ->multiple()
                    ->preload(),
                Tables\Filters\SelectFilter::make('creator_id')
                    ->label(__('inventories::filament/clusters/products/resources/lot.table.filters.creator'))
                    ->relationship('creator', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('company_id')
                    ->label(__('inventories::filament/clusters/products/resources/lot.table.filters.company'))
                    ->relationship('company', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()
                        ->action(function (Lot $record) {
                            try {
                                $record->delete();
                            } catch (QueryException $e) {
                                Notification::make()
                                    ->danger()
                                    ->title(__('inventories::filament/clusters/products/resources/lot.table.actions.delete.notification.error.title'))
                                    ->body(__('inventories::filament/clusters/products/resources/lot.table.actions.delete.notification.error.body'))
                                    ->send();
                            }
                        })
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('inventories::filament/clusters/products/resources/lot.table.actions.delete.notification.success.title'))
                                ->body(__('inventories::filament/clusters/products/resources/lot.table.actions.delete.notification.success.body')),
                        ),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('print')
                        ->label(__('inventories::filament/clusters/products/resources/lot.table.bulk-actions.print.label'))
                        ->icon('heroicon-o-printer')
                        ->action(function ($records) {
                            $pdf = PDF::loadView('inventories::filament.clusters.products.lots.actions.print', [
                                'records' => $records,
                            ]);

                            $pdf->setPaper('a4', 'portrait');

                            return response()->streamDownload(function () use ($pdf) {
                                echo $pdf->output();
                            }, 'Lot-Barcode.pdf');
                        }),
                    Tables\Actions\DeleteBulkAction::make()
                        ->action(function (Collection $records) {
                            try {
                                $records->each(fn (Model $record) => $record->delete());
                            } catch (QueryException $e) {
                                Notification::make()
                                    ->danger()
                                    ->title(__('inventories::filament/clusters/products/resources/lot.table.bulk-actions.delete.notification.error.title'))
                                    ->body(__('inventories::filament/clusters/products/resources/lot.table.bulk-actions.delete.notification.error.body'))
                                    ->send();
                            }
                        })
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('inventories::filament/clusters/products/resources/lot.table.bulk-actions.delete.notification.success.title'))
                                ->body(__('inventories::filament/clusters/products/resources/lot.table.bulk-actions.delete.notification.success.body')),
                        ),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Group::make()
                    ->schema([
                        Infolists\Components\Section::make(__('inventories::filament/clusters/products/resources/lot.infolist.sections.general.title'))
                            ->schema([
                                Infolists\Components\TextEntry::make('name')
                                    ->label(__('inventories::filament/clusters/products/resources/lot.infolist.sections.general.entries.name'))
                                    ->icon('heroicon-o-rectangle-stack')
                                    ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                                    ->weight(FontWeight::Bold),

                                Infolists\Components\Grid::make(2)
                                    ->schema([
                                        Infolists\Components\TextEntry::make('product.name')
                                            ->label(__('inventories::filament/clusters/products/resources/lot.infolist.sections.general.entries.product'))
                                            ->icon('heroicon-o-cube'),

                                        Infolists\Components\TextEntry::make('reference')
                                            ->label(__('inventories::filament/clusters/products/resources/lot.infolist.sections.general.entries.reference'))
                                            ->icon('heroicon-o-document-text')
                                            ->placeholder('—'),
                                    ]),

                                Infolists\Components\TextEntry::make('description')
                                    ->label(__('inventories::filament/clusters/products/resources/lot.infolist.sections.general.entries.description'))
                                    ->html()
                                    ->placeholder('—'),

                                Infolists\Components\Grid::make(2)
                                    ->schema([
                                        Infolists\Components\TextEntry::make('total_quantity')
                                            ->label(__('inventories::filament/clusters/products/resources/lot.infolist.sections.general.entries.on-hand-qty'))
                                            ->icon('heroicon-o-calculator')
                                            ->badge(),

                                        Infolists\Components\TextEntry::make('company.name')
                                            ->label(__('inventories::filament/clusters/products/resources/lot.infolist.sections.general.entries.company'))
                                            ->icon('heroicon-o-building-office'),
                                    ]),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),

                Infolists\Components\Group::make()
                    ->schema([
                        Infolists\Components\Section::make(__('inventories::filament/clusters/products/resources/lot.infolist.sections.record-information.title'))
                            ->schema([
                                Infolists\Components\TextEntry::make('created_at')
                                    ->label(__('inventories::filament/clusters/products/resources/lot.infolist.sections.record-information.entries.created-at'))
                                    ->dateTime()
                                    ->icon('heroicon-m-calendar'),

                                Infolists\Components\TextEntry::make('creator.name')
                                    ->label(__('inventories::filament/clusters/products/resources/lot.infolist.sections.record-information.entries.created-by'))
                                    ->icon('heroicon-m-user'),

                                Infolists\Components\TextEntry::make('updated_at')
                                    ->label(__('inventories::filament/clusters/products/resources/lot.infolist.sections.record-information.entries.last-updated'))
                                    ->dateTime()
                                    ->icon('heroicon-m-calendar-days'),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewLot::class,
            Pages\EditLot::class,
            Pages\ManageQuantities::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'      => Pages\ListLots::route('/'),
            'create'     => Pages\CreateLot::route('/create'),
            'view'       => Pages\ViewLot::route('/{record}'),
            'edit'       => Pages\EditLot::route('/{record}/edit'),
            'quantities' => Pages\ManageQuantities::route('/{record}/quantities'),
        ];
    }
}
