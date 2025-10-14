<?php

namespace Webkul\Inventory\Filament\Clusters\Products\Resources;

use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Webkul\Inventory\Enums\ProductTracking;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\DeliveryResource\Pages\EditDelivery;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\DropshipResource\Pages\EditDropship;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\InternalResource\Pages\EditInternal;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\ReceiptResource\Pages\EditReceipt;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\ScrapResource\Pages\CreateScrap;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\ScrapResource\Pages\EditScrap;
use Webkul\Inventory\Filament\Clusters\Products;
use Webkul\Inventory\Filament\Clusters\Products\Resources\LotResource\Pages;
use Webkul\Inventory\Filament\Clusters\Products\Resources\LotResource\Pages\CreateLot;
use Webkul\Inventory\Filament\Clusters\Products\Resources\LotResource\Pages\EditLot;
use Webkul\Inventory\Filament\Clusters\Products\Resources\LotResource\Pages\ListLots;
use Webkul\Inventory\Filament\Clusters\Products\Resources\LotResource\Pages\ViewLot;
use Webkul\Inventory\Filament\Clusters\Products\Resources\ProductResource\Pages\ManageQuantities;
use Webkul\Inventory\Models\Lot;
use Webkul\Inventory\Settings\TraceabilitySettings;

class LotResource extends Resource
{
    protected static ?string $model = Lot::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = Products::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 3;

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

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('inventories::filament/clusters/products/resources/lot.form.sections.general.title'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('inventories::filament/clusters/products/resources/lot.form.sections.general.fields.name'))
                            ->required()
                            ->maxLength(255)
                            ->autofocus()
                            ->placeholder(__('inventories::filament/clusters/products/resources/lot.form.sections.general.fields.name-placeholder'))
                            ->extraInputAttributes(['style' => 'font-size: 1.5rem;height: 3rem;']),
                        Group::make()
                            ->schema([
                                Select::make('product_id')
                                    ->label(__('inventories::filament/clusters/products/resources/lot.form.sections.general.fields.product'))
                                    ->relationship('product', 'name')
                                    ->relationship(
                                        name: 'product',
                                        titleAttribute: 'name',
                                        modifyQueryUsing: fn (Builder $query) => $query->where('tracking', ProductTracking::LOT)->whereNull('is_configurable'),
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
                                TextInput::make('reference')
                                    ->label(__('inventories::filament/clusters/products/resources/lot.form.sections.general.fields.reference'))
                                    ->maxLength(255)
                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('inventories::filament/clusters/products/resources/lot.form.sections.general.fields.reference-hint-tooltip')),
                                RichEditor::make('description')
                                    ->label(__('inventories::filament/clusters/products/resources/lot.form.sections.general.fields.description'))
                                    ->columnSpan(2),
                            ])
                            ->columns(2),
                    ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('inventories::filament/clusters/products/resources/lot.table.columns.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('product.name')
                    ->label(__('inventories::filament/clusters/products/resources/lot.table.columns.product'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('reference')
                    ->label(__('inventories::filament/clusters/products/resources/lot.table.columns.reference'))
                    ->placeholder('—')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('total_quantity')
                    ->label(__('inventories::filament/clusters/products/resources/lot.table.columns.on-hand-qty'))
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('inventories::filament/clusters/products/resources/lot.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
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
                SelectFilter::make('product_id')
                    ->label(__('inventories::filament/clusters/products/resources/lot.table.filters.product'))
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('location_id')
                    ->label(__('inventories::filament/clusters/products/resources/lot.table.filters.location'))
                    ->relationship('location', 'full_name')
                    ->searchable()
                    ->multiple()
                    ->preload(),
                SelectFilter::make('creator_id')
                    ->label(__('inventories::filament/clusters/products/resources/lot.table.filters.creator'))
                    ->relationship('creator', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('company_id')
                    ->label(__('inventories::filament/clusters/products/resources/lot.table.filters.company'))
                    ->relationship('company', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make()
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
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('print')
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
                    DeleteBulkAction::make()
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

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make(__('inventories::filament/clusters/products/resources/lot.infolist.sections.general.title'))
                            ->schema([
                                TextEntry::make('name')
                                    ->label(__('inventories::filament/clusters/products/resources/lot.infolist.sections.general.entries.name'))
                                    ->icon('heroicon-o-rectangle-stack')
                                    ->size(TextSize::Large)
                                    ->weight(FontWeight::Bold),

                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('product.name')
                                            ->label(__('inventories::filament/clusters/products/resources/lot.infolist.sections.general.entries.product'))
                                            ->icon('heroicon-o-cube'),

                                        TextEntry::make('reference')
                                            ->label(__('inventories::filament/clusters/products/resources/lot.infolist.sections.general.entries.reference'))
                                            ->icon('heroicon-o-document-text')
                                            ->placeholder('—'),
                                    ]),

                                TextEntry::make('description')
                                    ->label(__('inventories::filament/clusters/products/resources/lot.infolist.sections.general.entries.description'))
                                    ->html()
                                    ->placeholder('—'),

                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('total_quantity')
                                            ->label(__('inventories::filament/clusters/products/resources/lot.infolist.sections.general.entries.on-hand-qty'))
                                            ->icon('heroicon-o-calculator')
                                            ->badge(),

                                        TextEntry::make('company.name')
                                            ->label(__('inventories::filament/clusters/products/resources/lot.infolist.sections.general.entries.company'))
                                            ->icon('heroicon-o-building-office'),
                                    ]),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make(__('inventories::filament/clusters/products/resources/lot.infolist.sections.record-information.title'))
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label(__('inventories::filament/clusters/products/resources/lot.infolist.sections.record-information.entries.created-at'))
                                    ->dateTime()
                                    ->icon('heroicon-m-calendar'),

                                TextEntry::make('creator.name')
                                    ->label(__('inventories::filament/clusters/products/resources/lot.infolist.sections.record-information.entries.created-by'))
                                    ->icon('heroicon-m-user'),

                                TextEntry::make('updated_at')
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
            ViewLot::class,
            EditLot::class,
            Pages\ManageQuantities::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'      => ListLots::route('/'),
            'create'     => CreateLot::route('/create'),
            'view'       => ViewLot::route('/{record}'),
            'edit'       => EditLot::route('/{record}/edit'),
            'quantities' => Pages\ManageQuantities::route('/{record}/quantities'),
        ];
    }
}
