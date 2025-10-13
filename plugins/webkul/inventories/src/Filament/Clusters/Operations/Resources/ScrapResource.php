<?php

namespace Webkul\Inventory\Filament\Clusters\Operations\Resources;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\NumberConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\Tables\Filters\QueryBuilder\Constraints\SelectConstraint;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Webkul\Field\Filament\Forms\Components\ProgressStepper;
use Webkul\Inventory\Enums\LocationType;
use Webkul\Inventory\Enums\OperationState;
use Webkul\Inventory\Enums\ProductTracking;
use Webkul\Inventory\Enums\ScrapState;
use Webkul\Inventory\Filament\Clusters\Operations;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\ScrapResource\Pages\CreateScrap;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\ScrapResource\Pages\EditScrap;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\ScrapResource\Pages\ListScraps;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\ScrapResource\Pages\ManageMoves;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\ScrapResource\Pages\ViewScrap;
use Webkul\Inventory\Filament\Clusters\Products\Resources\LotResource;
use Webkul\Inventory\Filament\Clusters\Products\Resources\PackageResource;
use Webkul\Inventory\Filament\Clusters\Products\Resources\ProductResource;
use Webkul\Inventory\Models\Location;
use Webkul\Inventory\Models\Product;
use Webkul\Inventory\Models\Scrap;
use Webkul\Inventory\Settings\OperationSettings;
use Webkul\Inventory\Settings\ProductSettings;
use Webkul\Inventory\Settings\TraceabilitySettings;
use Webkul\Inventory\Settings\WarehouseSettings;
use Webkul\Partner\Filament\Resources\PartnerResource;
use Webkul\Product\Enums\ProductType;

class ScrapResource extends Resource
{
    protected static ?string $model = Scrap::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-trash';

    protected static ?int $navigationSort = 5;

    protected static ?string $cluster = Operations::class;

    public static function getNavigationLabel(): string
    {
        return __('inventories::filament/clusters/operations/resources/scrap.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('inventories::filament/clusters/operations/resources/scrap.navigation.group');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                ProgressStepper::make('state')
                    ->hiddenLabel()
                    ->inline()
                    ->options(ScrapState::options())
                    ->default(ScrapState::DRAFT)
                    ->disabled(),
                Section::make(__('inventories::filament/clusters/operations/resources/scrap.form.sections.general.title'))
                    ->schema([
                        Group::make()
                            ->schema([
                                Group::make()
                                    ->schema([
                                        Select::make('product_id')
                                            ->label(__('inventories::filament/clusters/operations/resources/scrap.form.sections.general.fields.product'))
                                            ->relationship(name: 'product', titleAttribute: 'name')
                                            ->relationship(
                                                'product',
                                                'name',
                                                fn ($query) => $query->where('type', ProductType::GOODS)->whereNull('is_configurable'),
                                            )
                                            ->getOptionLabelFromRecordUsing(function ($record): string {
                                                return $record->name.($record->trashed() ? ' (Deleted)' : '');
                                            })
                                            ->disableOptionWhen(function ($label) {
                                                return str_contains($label, ' (Deleted)');
                                            })
                                            ->searchable()
                                            ->preload()
                                            ->live()
                                            ->afterStateUpdated(function (Set $set, Get $get) {
                                                $set('lot_id', null);

                                                if ($product = Product::find($get('product_id'))) {
                                                    $set('uom_id', $product->uom_id);
                                                }
                                            })
                                            ->createOptionForm(fn (Schema $schema): Schema => ProductResource::form($schema))
                                            ->createOptionAction(fn ($action) => $action->modalWidth('6xl'))
                                            ->disabled(fn ($record): bool => $record?->state == ScrapState::DONE),
                                        TextInput::make('qty')
                                            ->label(__('inventories::filament/clusters/operations/resources/scrap.form.sections.general.fields.quantity'))
                                            ->required()
                                            ->numeric()
                                            ->minValue(0)
                                            ->maxValue(99999999999)
                                            ->default(0)
                                            ->disabled(fn ($record): bool => $record?->state == ScrapState::DONE),
                                        Select::make('uom_id')
                                            ->label(__('inventories::filament/clusters/operations/resources/scrap.form.sections.general.fields.unit'))
                                            ->relationship(
                                                'uom',
                                                'name',
                                                fn ($query) => $query->where('category_id', 1),
                                            )
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->visible(static::getProductSettings()->enable_uom)
                                            ->disabled(fn ($record): bool => $record?->state == ScrapState::DONE),
                                        Select::make('lot_id')
                                            ->label(__('inventories::filament/clusters/operations/resources/scrap.form.sections.general.fields.lot'))
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->relationship(
                                                name: 'lot',
                                                titleAttribute: 'name',
                                                modifyQueryUsing: fn (Builder $query, Get $get) => $query->where('product_id', $get('product_id')),
                                            )
                                            ->disabled(fn ($record): bool => $record?->state == ScrapState::DONE)
                                            ->visible(function (Get $get): bool {
                                                if (! static::getTraceabilitySettings()->enable_lots_serial_numbers) {
                                                    return false;
                                                }

                                                $product = Product::find($get('product_id'));

                                                if (! $product) {
                                                    return false;
                                                }

                                                return $product->tracking === ProductTracking::LOT;
                                            })
                                            ->createOptionForm(fn (Schema $schema): Schema => LotResource::form($schema))
                                            ->createOptionAction(function (Action $action, Get $get) {
                                                $action
                                                    ->mutateDataUsing(function (array $data) use ($get): array {
                                                        $data['product_id'] = $get('product_id');

                                                        return $data;
                                                    });
                                            }),
                                        Select::make('tags')
                                            ->label(__('inventories::filament/clusters/operations/resources/scrap.form.sections.general.fields.tags'))
                                            ->relationship(name: 'tags', titleAttribute: 'name')
                                            ->multiple()
                                            ->searchable()
                                            ->preload()
                                            ->createOptionForm([
                                                TextInput::make('name')
                                                    ->label(__('inventories::filament/clusters/operations/resources/scrap.form.sections.general.fields.name'))
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->unique('inventories_tags'),
                                            ]),
                                    ]),

                                Group::make()
                                    ->schema([
                                        Select::make('package_id')
                                            ->label(__('inventories::filament/clusters/operations/resources/scrap.form.sections.general.fields.package'))
                                            ->relationship('package', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->createOptionForm(fn (Schema $schema): Schema => PackageResource::form($schema))
                                            ->visible(static::getOperationSettings()->enable_packages)
                                            ->disabled(fn ($record): bool => $record?->state == ScrapState::DONE),
                                        Select::make('partner_id')
                                            ->label(__('inventories::filament/clusters/operations/resources/scrap.form.sections.general.fields.owner'))
                                            ->relationship('partner', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->createOptionForm(fn (Schema $schema): Schema => PartnerResource::form($schema))
                                            ->disabled(fn ($record): bool => $record?->state == ScrapState::DONE),
                                        Select::make('source_location_id')
                                            ->label(__('inventories::filament/clusters/operations/resources/scrap.form.sections.general.fields.source-location'))
                                            ->relationship('sourceLocation', 'full_name')
                                            ->relationship(
                                                'sourceLocation',
                                                'full_name',
                                                fn ($query) => $query->where('type', LocationType::INTERNAL)->where('is_scrap', false),
                                            )
                                            ->required()
                                            ->searchable()
                                            ->preload()
                                            ->default(function () {
                                                $scrapLocation = Location::where('type', LocationType::INTERNAL)
                                                    ->where('is_scrap', false)
                                                    ->first();

                                                return $scrapLocation?->id;
                                            })
                                            ->visible(static::getWarehouseSettings()->enable_locations)
                                            ->disabled(fn ($record): bool => $record?->state == ScrapState::DONE),
                                        Select::make('destination_location_id')
                                            ->label(__('inventories::filament/clusters/operations/resources/scrap.form.sections.general.fields.destination-location'))
                                            ->relationship('destinationLocation', 'full_name')
                                            ->relationship(
                                                'destinationLocation',
                                                'full_name',
                                                fn ($query) => $query->where('is_scrap', true),
                                            )
                                            ->required()
                                            ->searchable()
                                            ->preload()
                                            ->default(function () {
                                                $scrapLocation = Location::where('is_scrap', true)
                                                    ->first();

                                                return $scrapLocation?->id;
                                            })
                                            ->visible(static::getWarehouseSettings()->enable_locations)
                                            ->disabled(fn ($record): bool => $record?->state == ScrapState::DONE),
                                        TextInput::make('origin')
                                            ->label(__('inventories::filament/clusters/operations/resources/scrap.form.sections.general.fields.source-document'))
                                            ->maxLength(255),
                                        Select::make('company_id')
                                            ->label(__('inventories::filament/clusters/operations/resources/scrap.form.sections.general.fields.company'))
                                            ->relationship('company', 'name')
                                            ->required()
                                            ->searchable()
                                            ->preload()
                                            ->default(Auth::user()->default_company_id)
                                            ->disabled(fn ($record): bool => $record?->state == ScrapState::DONE),
                                    ]),
                            ])
                            ->columns(2),
                    ]),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('closed_at')
                    ->label(__('inventories::filament/clusters/operations/resources/scrap.table.columns.date'))
                    ->sortable()
                    ->date(),
                TextColumn::make('name')
                    ->label(__('inventories::filament/clusters/operations/resources/scrap.table.columns.reference'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('product.name')
                    ->label(__('inventories::filament/clusters/operations/resources/scrap.table.columns.product'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('lot.name')
                    ->label(__('inventories::filament/clusters/operations/resources/scrap.table.columns.lot'))
                    ->searchable()
                    ->sortable()
                    ->placeholder('—')
                    ->visible(static::getTraceabilitySettings()->enable_lots_serial_numbers),
                TextColumn::make('package.name')
                    ->label(__('inventories::filament/clusters/operations/resources/scrap.table.columns.package'))
                    ->searchable()
                    ->sortable()
                    ->placeholder('—')
                    ->visible(static::getOperationSettings()->enable_packages),
                TextColumn::make('sourceLocation.full_name')
                    ->label(__('inventories::filament/clusters/operations/resources/scrap.table.columns.source-location'))
                    ->sortable()
                    ->visible(static::getWarehouseSettings()->enable_locations),
                TextColumn::make('destinationLocation.full_name')
                    ->label(__('inventories::filament/clusters/operations/resources/scrap.table.columns.scrap-location'))
                    ->sortable()
                    ->visible(static::getWarehouseSettings()->enable_locations),
                TextColumn::make('qty')
                    ->label(__('inventories::filament/clusters/operations/resources/scrap.table.columns.quantity'))
                    ->sortable(),
                TextColumn::make('uom.name')
                    ->label(__('inventories::filament/clusters/operations/resources/scrap.table.columns.uom'))
                    ->sortable()
                    ->visible(static::getWarehouseSettings()->enable_locations),
                TextColumn::make('state')
                    ->label(__('inventories::filament/clusters/operations/resources/scrap.table.columns.state'))
                    ->sortable()
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups(
                collect([
                    Tables\Grouping\Group::make('product.name')
                        ->label(__('inventories::filament/clusters/operations/resources/scrap.table.groups.product')),
                    Tables\Grouping\Group::make('sourceLocation.full_name')
                        ->label(__('inventories::filament/clusters/operations/resources/scrap.table.groups.source-location')),
                    Tables\Grouping\Group::make('destinationLocation.full_name')
                        ->label(__('inventories::filament/clusters/operations/resources/scrap.table.groups.destination-location')),
                ])->filter(function ($group) {
                    return match ($group->getId()) {
                        'sourceLocation.full_name', 'destinationLocation.full_name' => static::getWarehouseSettings()->enable_locations,
                        default => true
                    };
                })->all()
            )
            ->filters([
                QueryBuilder::make()
                    ->constraints(collect([
                        static::getWarehouseSettings()->enable_locations
                            ? RelationshipConstraint::make('sourceLocation')
                                ->label(__('inventories::filament/clusters/operations/resources/scrap.table.filters.source-location'))
                                ->multiple()
                                ->selectable(
                                    IsRelatedToOperator::make()
                                        ->titleAttribute('full_name')
                                        ->searchable()
                                        ->multiple()
                                        ->preload(),
                                )
                                ->icon('heroicon-o-map-pin')
                            : null,
                        static::getWarehouseSettings()->enable_locations
                            ? RelationshipConstraint::make('destinationLocation')
                                ->label(__('inventories::filament/clusters/operations/resources/scrap.table.filters.destination-location'))
                                ->multiple()
                                ->selectable(
                                    IsRelatedToOperator::make()
                                        ->titleAttribute('full_name')
                                        ->searchable()
                                        ->multiple()
                                        ->preload(),
                                )
                                ->icon('heroicon-o-map-pin')
                            : null,
                        SelectConstraint::make('state')
                            ->label(__('inventories::filament/clusters/operations/resources/scrap.table.filters.state'))
                            ->multiple()
                            ->options(OperationState::class)
                            ->icon('heroicon-o-bars-2'),
                        RelationshipConstraint::make('product')
                            ->label(__('inventories::filament/clusters/operations/resources/scrap.table.filters.product'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-shopping-bag'),
                        static::getProductSettings()->enable_uom
                            ? RelationshipConstraint::make('uom')
                                ->label(__('inventories::filament/clusters/operations/resources/scrap.table.filters.uom'))
                                ->multiple()
                                ->selectable(
                                    IsRelatedToOperator::make()
                                        ->titleAttribute('name')
                                        ->searchable()
                                        ->multiple()
                                        ->preload(),
                                )
                                ->icon('heroicon-o-shopping-bag')
                            : null,
                        RelationshipConstraint::make('product.category')
                            ->label(__('inventories::filament/clusters/operations/resources/scrap.table.filters.product-category'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('full_name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-folder'),
                        static::getTraceabilitySettings()->enable_lots_serial_numbers
                            ? RelationshipConstraint::make('lot')
                                ->label(__('inventories::filament/clusters/operations/resources/scrap.table.filters.lot'))
                                ->multiple()
                                ->selectable(
                                    IsRelatedToOperator::make()
                                        ->titleAttribute('name')
                                        ->searchable()
                                        ->multiple()
                                        ->preload(),
                                )
                                ->icon('heroicon-o-rectangle-stack')
                            : null,
                        static::getOperationSettings()->enable_packages
                            ? RelationshipConstraint::make('package')
                                ->label(__('inventories::filament/clusters/operations/resources/scrap.table.filters.package'))
                                ->multiple()
                                ->selectable(
                                    IsRelatedToOperator::make()
                                        ->titleAttribute('name')
                                        ->searchable()
                                        ->multiple()
                                        ->preload(),
                                )
                                ->icon('heroicon-o-cube')
                            : null,
                        NumberConstraint::make('qty')
                            ->label(__('inventories::filament/clusters/operations/resources/scrap.table.filters.quantity'))
                            ->icon('heroicon-o-scale'),
                        DateConstraint::make('closed_at')
                            ->label(__('inventories::filament/clusters/operations/resources/scrap.table.filters.closed-at')),
                        DateConstraint::make('created_at')
                            ->label(__('inventories::filament/clusters/operations/resources/scrap.table.filters.created-at')),
                        DateConstraint::make('updated_at')
                            ->label(__('inventories::filament/clusters/operations/resources/scrap.table.filters.updated-at')),
                        RelationshipConstraint::make('company')
                            ->label(__('inventories::filament/clusters/operations/resources/scrap.table.filters.company'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-building-office'),
                        RelationshipConstraint::make('creator')
                            ->label(__('inventories::filament/clusters/operations/resources/scrap.table.filters.creator'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-user'),
                    ])->filter()->values()->all()),
            ], layout: FiltersLayout::Modal)
            ->filtersTriggerAction(
                fn (Action $action) => $action
                    ->slideOver(),
            )
            ->filtersFormColumns(2)
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make()
                        ->hidden(fn (Scrap $record): bool => $record->state == ScrapState::DONE)
                        ->action(function (Scrap $record) {
                            try {
                                $record->delete();
                            } catch (QueryException $e) {
                                Notification::make()
                                    ->danger()
                                    ->title(__('inventories::filament/clusters/operations/resources/scrap.table.actions.delete.notification.error.title'))
                                    ->body(__('inventories::filament/clusters/operations/resources/scrap.table.actions.delete.notification.error.body'))
                                    ->send();
                            }
                        })
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('inventories::filament/clusters/operations/resources/scrap.table.actions.delete.notification.success.title'))
                                ->body(__('inventories::filament/clusters/operations/resources/scrap.table.actions.delete.notification.success.body')),
                        ),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->action(function (Collection $records) {
                            try {
                                $records->each(fn (Model $record) => $record->delete());
                            } catch (QueryException $e) {
                                Notification::make()
                                    ->danger()
                                    ->title(__('inventories::filament/clusters/operations/resources/scrap.table.bulk-actions.delete.notification.error.title'))
                                    ->body(__('inventories::filament/clusters/operations/resources/scrap.table.bulk-actions.delete.notification.error.body'))
                                    ->send();
                            }
                        })
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('inventories::filament/clusters/operations/resources/scrap.table.bulk-actions.delete.notification.success.title'))
                                ->body(__('inventories::filament/clusters/operations/resources/scrap.table.bulk-actions.delete.notification.success.body')),
                        ),
                ]),
            ])
            ->checkIfRecordIsSelectableUsing(
                fn (Model $record): bool => static::can('delete', $record) && $record->state !== ScrapState::DONE,
            );
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make(__('inventories::filament/clusters/operations/resources/scrap.infolist.sections.general.title'))
                            ->schema([
                                Group::make()
                                    ->schema([
                                        Group::make()
                                            ->schema([
                                                TextEntry::make('product.name')
                                                    ->label(__('inventories::filament/clusters/operations/resources/scrap.infolist.sections.general.entries.product'))
                                                    ->icon('heroicon-o-shopping-bag'),

                                                TextEntry::make('qty')
                                                    ->label(__('inventories::filament/clusters/operations/resources/scrap.infolist.sections.general.entries.quantity'))
                                                    ->icon('heroicon-o-calculator')
                                                    ->suffix(fn (Scrap $record) => ' '.$record->uom?->name),

                                                TextEntry::make('lot.name')
                                                    ->label(__('inventories::filament/clusters/operations/resources/scrap.infolist.sections.general.entries.lot'))
                                                    ->icon('heroicon-o-rectangle-stack')
                                                    ->placeholder('—')
                                                    ->visible(static::getTraceabilitySettings()->enable_lots_serial_numbers),

                                                TextEntry::make('tags.name')
                                                    ->label(__('inventories::filament/clusters/operations/resources/scrap.infolist.sections.general.entries.tags'))
                                                    ->icon('heroicon-o-tag')
                                                    ->badge()
                                                    ->separator(','),
                                            ]),

                                        Group::make()
                                            ->schema([
                                                TextEntry::make('package.name')
                                                    ->label(__('inventories::filament/clusters/operations/resources/scrap.infolist.sections.general.entries.package'))
                                                    ->icon('heroicon-o-cube')
                                                    ->placeholder('—')
                                                    ->visible(static::getOperationSettings()->enable_packages),

                                                TextEntry::make('partner.name')
                                                    ->label(__('inventories::filament/clusters/operations/resources/scrap.infolist.sections.general.entries.owner'))
                                                    ->icon('heroicon-o-user-circle'),

                                                TextEntry::make('sourceLocation.full_name')
                                                    ->label(__('inventories::filament/clusters/operations/resources/scrap.infolist.sections.general.entries.source-location'))
                                                    ->icon('heroicon-o-map-pin'),

                                                TextEntry::make('destinationLocation.full_name')
                                                    ->label(__('inventories::filament/clusters/operations/resources/scrap.infolist.sections.general.entries.destination-location'))
                                                    ->icon('heroicon-o-map-pin'),

                                                TextEntry::make('origin')
                                                    ->label(__('inventories::filament/clusters/operations/resources/scrap.infolist.sections.general.entries.source-document'))
                                                    ->icon('heroicon-o-document-text')
                                                    ->placeholder('—'),

                                                TextEntry::make('company.name')
                                                    ->label(__('inventories::filament/clusters/operations/resources/scrap.infolist.sections.general.entries.company'))
                                                    ->icon('heroicon-o-building-office'),
                                            ]),
                                    ])
                                    ->columns(2),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make(__('inventories::filament/clusters/operations/resources/scrap.infolist.sections.record-information.title'))
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label(__('inventories::filament/clusters/operations/resources/scrap.infolist.sections.record-information.entries.created-at'))
                                    ->dateTime()
                                    ->icon('heroicon-m-calendar'),

                                TextEntry::make('creator.name')
                                    ->label(__('inventories::filament/clusters/operations/resources/scrap.infolist.sections.record-information.entries.created-by'))
                                    ->icon('heroicon-m-user'),

                                TextEntry::make('updated_at')
                                    ->label(__('inventories::filament/clusters/operations/resources/scrap.infolist.sections.record-information.entries.last-updated'))
                                    ->dateTime()
                                    ->icon('heroicon-m-calendar-days'),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function getOperationSettings(): OperationSettings
    {
        return once(fn () => app(OperationSettings::class));
    }

    public static function getProductSettings(): ProductSettings
    {
        return once(fn () => app(ProductSettings::class));
    }

    public static function getTraceabilitySettings(): TraceabilitySettings
    {
        return once(fn () => app(TraceabilitySettings::class));
    }

    public static function getWarehouseSettings(): WarehouseSettings
    {
        return once(fn () => app(WarehouseSettings::class));
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewScrap::class,
            EditScrap::class,
            ManageMoves::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListScraps::route('/'),
            'create' => CreateScrap::route('/create'),
            'view'   => ViewScrap::route('/{record}/view'),
            'edit'   => EditScrap::route('/{record}/edit'),
            'moves'  => ManageMoves::route('/{record}/moves'),
        ];
    }
}
