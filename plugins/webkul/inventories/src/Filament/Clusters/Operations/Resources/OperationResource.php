<?php

namespace Webkul\Inventory\Filament\Clusters\Operations\Resources;

use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Webkul\Field\Filament\Forms\Components\ProgressStepper;
use Webkul\Field\Filament\Traits\HasCustomFields;
use Webkul\Inventory\Enums;
use Webkul\Inventory\Filament\Clusters\Operations\Resources;
use Webkul\Inventory\Filament\Clusters\Products\Resources\LotResource;
use Webkul\Inventory\Filament\Clusters\Products\Resources\PackageResource;
use Webkul\Inventory\Models\Move;
use Webkul\Inventory\Models\Operation;
use Webkul\Inventory\Models\OperationType;
use Webkul\Inventory\Models\Packaging;
use Webkul\Inventory\Models\Product;
use Webkul\Inventory\Models\ProductQuantity;
use Webkul\Inventory\Settings;
use Webkul\Partner\Filament\Resources\AddressResource;
use Webkul\Partner\Filament\Resources\PartnerResource;
use Webkul\Product\Enums\ProductType;
use Webkul\Support\Models\UOM;
use Webkul\TableViews\Filament\Components\PresetView;
use Webkul\Inventory\Facades\Inventory;

class OperationResource extends Resource
{
    use HasCustomFields;

    protected static ?string $model = Operation::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                ProgressStepper::make('state')
                    ->hiddenLabel()
                    ->inline()
                    ->options(Enums\OperationState::options())
                    ->options(function ($record) {
                        $options = Enums\OperationState::options();

                        if ($record && $record->state !== Enums\OperationState::CANCELED) {
                            unset($options[Enums\OperationState::CANCELED->value]);
                        }

                        return $options;
                    })
                    ->default(Enums\OperationState::DRAFT)
                    ->disabled(),
                Forms\Components\Section::make(__('inventories::filament/clusters/operations/resources/operation.form.sections.general.title'))
                    ->schema([
                        Forms\Components\Select::make('partner_id')
                            ->label(__('inventories::filament/clusters/operations/resources/operation.form.sections.general.fields.receive-from'))
                            ->relationship('partner', 'name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm(fn (Form $form): Form => PartnerResource::form($form))
                            ->visible(fn (Forms\Get $get): bool => OperationType::find($get('operation_type_id'))?->type == Enums\OperationType::INCOMING)
                            ->disabled(fn ($record): bool => in_array($record?->state, [Enums\OperationState::DONE, Enums\OperationState::CANCELED])),
                        Forms\Components\Select::make('partner_id')
                            ->label(__('inventories::filament/clusters/operations/resources/operation.form.sections.general.fields.contact'))
                            ->relationship('partner', 'name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm(fn (Form $form): Form => PartnerResource::form($form))
                            ->visible(fn (Forms\Get $get): bool => OperationType::find($get('operation_type_id'))?->type == Enums\OperationType::INTERNAL)
                            ->disabled(fn ($record): bool => in_array($record?->state, [Enums\OperationState::DONE, Enums\OperationState::CANCELED])),
                        Forms\Components\Select::make('partner_address_id')
                            ->label(__('inventories::filament/clusters/operations/resources/operation.form.sections.general.fields.delivery-address'))
                            ->relationship('partnerAddress', 'name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm(fn (Form $form): Form => AddressResource::form($form))
                            ->visible(fn (Forms\Get $get): bool => OperationType::find($get('operation_type_id'))?->type == Enums\OperationType::OUTGOING)
                            ->disabled(fn ($record): bool => in_array($record?->state, [Enums\OperationState::DONE, Enums\OperationState::CANCELED])),
                        Forms\Components\Select::make('operation_type_id')
                            ->label(__('inventories::filament/clusters/operations/resources/operation.form.sections.general.fields.operation-type'))
                            ->relationship('operationType', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->getOptionLabelFromRecordUsing(function (OperationType $record) {
                                if (! $record->warehouse) {
                                    return $record->name;
                                }

                                return $record->warehouse->name.': '.$record->name;
                            })
                            ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get) {
                                $operationType = OperationType::find($get('operation_type_id'));

                                $set('source_location_id', $operationType->source_location_id);
                                $set('destination_location_id', $operationType->destination_location_id);
                            })
                            ->disabled(fn ($record): bool => in_array($record?->state, [Enums\OperationState::DONE, Enums\OperationState::CANCELED])),
                        Forms\Components\Select::make('source_location_id')
                            ->label(__('inventories::filament/clusters/operations/resources/operation.form.sections.general.fields.source-location'))
                            ->relationship('sourceLocation', 'full_name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->visible(fn (Settings\WarehouseSettings $settings, Forms\Get $get): bool => $settings->enable_locations && OperationType::find($get('operation_type_id'))?->type != Enums\OperationType::INCOMING)
                            ->disabled(fn ($record): bool => in_array($record?->state, [Enums\OperationState::DONE, Enums\OperationState::CANCELED])),
                        Forms\Components\Select::make('destination_location_id')
                            ->label(__('inventories::filament/clusters/operations/resources/operation.form.sections.general.fields.destination-location'))
                            ->relationship('destinationLocation', 'full_name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->visible(fn (Settings\WarehouseSettings $settings, Forms\Get $get): bool => $settings->enable_locations && OperationType::find($get('operation_type_id'))?->type != Enums\OperationType::OUTGOING)
                            ->disabled(fn ($record): bool => in_array($record?->state, [Enums\OperationState::DONE, Enums\OperationState::CANCELED])),
                    ])
                    ->columns(2),

                Forms\Components\Tabs::make()
                    ->schema([
                        Forms\Components\Tabs\Tab::make(__('inventories::filament/clusters/operations/resources/operation.form.tabs.operations.title'))
                            ->schema([
                                static::getMovesRepeater(),
                            ]),

                        Forms\Components\Tabs\Tab::make(__('inventories::filament/clusters/operations/resources/operation.form.tabs.additional.title'))
                            ->schema([
                                Forms\Components\Select::make('user_id')
                                    ->label(__('inventories::filament/clusters/operations/resources/operation.form.tabs.additional.fields.responsible'))
                                    ->relationship('user', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->default(Auth::id())
                                    ->disabled(fn ($record): bool => in_array($record?->state, [Enums\OperationState::DONE, Enums\OperationState::CANCELED])),
                                Forms\Components\Select::make('move_type')
                                    ->label(__('inventories::filament/clusters/operations/resources/operation.form.tabs.additional.fields.shipping-policy'))
                                    ->options(Enums\MoveType::class)
                                    ->default(Enums\MoveType::DIRECT)
                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('inventories::filament/clusters/operations/resources/operation.form.tabs.additional.fields.shipping-policy-hint-tooltip'))
                                    ->visible(fn (Forms\Get $get): bool => OperationType::find($get('operation_type_id'))?->type != Enums\OperationType::INCOMING)
                                    ->disabled(fn ($record): bool => in_array($record?->state, [Enums\OperationState::DONE, Enums\OperationState::CANCELED])),
                                Forms\Components\DateTimePicker::make('scheduled_at')
                                    ->label(__('inventories::filament/clusters/operations/resources/operation.form.tabs.additional.fields.scheduled-at'))
                                    ->native(false)
                                    ->default(now()->format('Y-m-d H:i:s'))
                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('inventories::filament/clusters/operations/resources/operation.form.tabs.additional.fields.scheduled-at-hint-tooltip'))
                                    ->disabled(fn ($record): bool => in_array($record?->state, [Enums\OperationState::DONE, Enums\OperationState::CANCELED])),
                                Forms\Components\TextInput::make('origin')
                                    ->label(__('inventories::filament/clusters/operations/resources/operation.form.tabs.additional.fields.source-document'))
                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('inventories::filament/clusters/operations/resources/operation.form.tabs.additional.fields.source-document-hint-tooltip'))
                                    ->disabled(fn ($record): bool => in_array($record?->state, [Enums\OperationState::DONE, Enums\OperationState::CANCELED])),
                            ])
                            ->columns(2),

                        Forms\Components\Tabs\Tab::make(__('inventories::filament/clusters/operations/resources/operation.form.tabs.note.title'))
                            ->schema([
                                Forms\Components\RichEditor::make('description')
                                    ->hiddenLabel(),
                            ]),
                    ]),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\IconColumn::make('is_favorite')
                    ->label('')
                    ->icon(fn (Operation $record): string => $record->is_favorite ? 'heroicon-s-star' : 'heroicon-o-star')
                    ->color(fn (Operation $record): string => $record->is_favorite ? 'warning' : 'gray')
                    ->action(function (Operation $record): void {
                        $record->update([
                            'is_favorite' => ! $record->is_favorite,
                        ]);
                    }),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('inventories::filament/clusters/operations/resources/operation.table.columns.reference'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sourceLocation.full_name')
                    ->label(__('inventories::filament/clusters/operations/resources/operation.table.columns.from'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->visible(fn (Settings\WarehouseSettings $settings): bool => $settings->enable_locations),
                Tables\Columns\TextColumn::make('destinationLocation.full_name')
                    ->label(__('inventories::filament/clusters/operations/resources/operation.table.columns.to'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->visible(fn (Settings\WarehouseSettings $settings): bool => $settings->enable_locations),
                Tables\Columns\TextColumn::make('partner.name')
                    ->label(__('inventories::filament/clusters/operations/resources/operation.table.columns.contact'))
                    ->placeholder('—')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('inventories::filament/clusters/operations/resources/operation.table.columns.responsible'))
                    ->placeholder('—')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('scheduled_at')
                    ->label(__('inventories::filament/clusters/operations/resources/operation.table.columns.scheduled-at'))
                    ->placeholder('—')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('deadline')
                    ->label(__('inventories::filament/clusters/operations/resources/operation.table.columns.deadline'))
                    ->placeholder('—')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('closed_at')
                    ->label(__('inventories::filament/clusters/operations/resources/operation.table.columns.closed-at'))
                    ->placeholder('—')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('origin')
                    ->label(__('inventories::filament/clusters/operations/resources/operation.table.columns.source-document'))
                    ->placeholder('—')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('operationType.name')
                    ->label(__('inventories::filament/clusters/operations/resources/operation.table.columns.operation-type'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('company.name')
                    ->label(__('inventories::filament/clusters/operations/resources/operation.table.columns.company'))
                    ->placeholder('—')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('state')
                    ->label(__('inventories::filament/clusters/operations/resources/operation.table.columns.state'))
                    ->searchable()
                    ->sortable()
                    ->badge(),
            ])
            ->groups([
                Tables\Grouping\Group::make('state')
                    ->label(__('inventories::filament/clusters/operations/resources/operation.table.groups.state')),
                Tables\Grouping\Group::make('origin')
                    ->label(__('inventories::filament/clusters/operations/resources/operation.table.groups.source-document')),
                Tables\Grouping\Group::make('operationType.name')
                    ->label(__('inventories::filament/clusters/operations/resources/operation.table.groups.operation-type')),
                Tables\Grouping\Group::make('schedule_at')
                    ->label(__('inventories::filament/clusters/operations/resources/operation.table.groups.schedule-at'))
                    ->date(),
                Tables\Grouping\Group::make('created_at')
                    ->label(__('inventories::filament/clusters/operations/resources/operation.table.groups.created-at'))
                    ->date(),
            ])
            ->filters([
                Tables\Filters\QueryBuilder::make()
                    ->constraints(collect(static::mergeCustomTableQueryBuilderConstraints([
                        Tables\Filters\QueryBuilder\Constraints\TextConstraint::make('name')
                            ->label(__('inventories::filament/clusters/operations/resources/operation.table.filters.name')),
                        Tables\Filters\QueryBuilder\Constraints\SelectConstraint::make('state')
                            ->label(__('inventories::filament/clusters/operations/resources/operation.table.filters.state'))
                            ->multiple()
                            ->options(Enums\OperationState::class)
                            ->icon('heroicon-o-bars-2'),
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('partner')
                            ->label(__('inventories::filament/clusters/operations/resources/operation.table.filters.partner'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-user'),
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('user')
                            ->label(__('inventories::filament/clusters/operations/resources/operation.table.filters.responsible'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-user'),
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('owner')
                            ->label(__('inventories::filament/clusters/operations/resources/operation.table.filters.owner'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-user'),
                        app(Settings\WarehouseSettings::class)->enable_locations
                            ? Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('sourceLocation')
                                ->label(__('inventories::filament/clusters/operations/resources/operation.table.filters.source-location'))
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
                        app(Settings\WarehouseSettings::class)->enable_locations
                            ? Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('destinationLocation')
                                ->label(__('inventories::filament/clusters/operations/resources/operation.table.filters.destination-location'))
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
                        Tables\Filters\QueryBuilder\Constraints\DateConstraint::make('deadline')
                            ->label(__('inventories::filament/clusters/operations/resources/operation.table.filters.deadline'))
                            ->icon('heroicon-o-calendar'),
                        Tables\Filters\QueryBuilder\Constraints\DateConstraint::make('scheduled_at')
                            ->label(__('inventories::filament/clusters/operations/resources/operation.table.filters.scheduled-at')),
                        Tables\Filters\QueryBuilder\Constraints\DateConstraint::make('closed_at')
                            ->label(__('inventories::filament/clusters/operations/resources/operation.table.filters.closed-at')),
                        Tables\Filters\QueryBuilder\Constraints\DateConstraint::make('created_at')
                            ->label(__('inventories::filament/clusters/operations/resources/operation.table.filters.created-at')),
                        Tables\Filters\QueryBuilder\Constraints\DateConstraint::make('updated_at')
                            ->label(__('inventories::filament/clusters/operations/resources/operation.table.filters.updated-at')),
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('company')
                            ->label(__('inventories::filament/clusters/operations/resources/operation.table.filters.company'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-building-office'),
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('creator')
                            ->label(__('inventories::filament/clusters/operations/resources/operation.table.filters.creator'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-user'),
                    ]))->filter()->values()->all()),
            ], layout: \Filament\Tables\Enums\FiltersLayout::Modal)
            ->filtersTriggerAction(
                fn (Tables\Actions\Action $action) => $action
                    ->slideOver(),
            )
            ->filtersTriggerAction(
                fn (Tables\Actions\Action $action) => $action
                    ->slideOver(),
            )
            ->filtersFormColumns(2)
            ->checkIfRecordIsSelectableUsing(
                fn (Model $record): bool => static::can('delete', $record) && $record->state !== Enums\OperationState::DONE,
            );
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make()
                    ->schema([
                        Infolists\Components\TextEntry::make('state')
                            ->badge(),
                    ])
                    ->compact(),

                Infolists\Components\Section::make(__('inventories::filament/clusters/operations/resources/operation.infolist.sections.general.title'))
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('partner.name')
                                    ->label(__('inventories::filament/clusters/operations/resources/operation.infolist.sections.general.entries.contact'))
                                    ->icon('heroicon-o-user-group')
                                    ->placeholder('—'),

                                Infolists\Components\TextEntry::make('operationType.name')
                                    ->label(__('inventories::filament/clusters/operations/resources/operation.infolist.sections.general.entries.operation-type'))
                                    ->icon('heroicon-o-clipboard-document-list'),

                                Infolists\Components\TextEntry::make('sourceLocation.full_name')
                                    ->label(__('inventories::filament/clusters/operations/resources/operation.infolist.sections.general.entries.source-location'))
                                    ->icon('heroicon-o-arrow-up-tray')
                                    ->visible(fn (Settings\WarehouseSettings $settings): bool => $settings->enable_locations),

                                Infolists\Components\TextEntry::make('destinationLocation.full_name')
                                    ->label(__('inventories::filament/clusters/operations/resources/operation.infolist.sections.general.entries.destination-location'))
                                    ->icon('heroicon-o-arrow-down-tray')
                                    ->visible(fn (Settings\WarehouseSettings $settings): bool => $settings->enable_locations),
                            ]),
                    ]),

                // Tabs Section
                Infolists\Components\Tabs::make('Details')
                    ->tabs([
                        // Operations Tab
                        Infolists\Components\Tabs\Tab::make(__('inventories::filament/clusters/operations/resources/operation.infolist.tabs.operations.title'))
                            ->schema([
                                Infolists\Components\RepeatableEntry::make('moves')
                                    ->schema([
                                        Infolists\Components\Grid::make(5)
                                            ->schema([
                                                Infolists\Components\TextEntry::make('product.name')
                                                    ->label(__('inventories::filament/clusters/operations/resources/operation.infolist.tabs.operations.entries.product'))
                                                    ->icon('heroicon-o-cube'),

                                                Infolists\Components\TextEntry::make('finalLocation.full_name')
                                                    ->label(__('inventories::filament/clusters/operations/resources/operation.infolist.tabs.operations.entries.final-location'))
                                                    ->icon('heroicon-o-map-pin')
                                                    ->placeholder('—')
                                                    ->visible(fn (Settings\WarehouseSettings $settings) => $settings->enable_locations),

                                                Infolists\Components\TextEntry::make('description')
                                                    ->label(__('inventories::filament/clusters/operations/resources/operation.infolist.tabs.operations.entries.description'))
                                                    ->icon('heroicon-o-document-text')
                                                    ->placeholder('—'),

                                                Infolists\Components\TextEntry::make('scheduled_at')
                                                    ->label(__('inventories::filament/clusters/operations/resources/operation.infolist.tabs.operations.entries.scheduled-at'))
                                                    ->dateTime()
                                                    ->icon('heroicon-o-calendar')
                                                    ->placeholder('—'),

                                                Infolists\Components\TextEntry::make('deadline')
                                                    ->label(__('inventories::filament/clusters/operations/resources/operation.infolist.tabs.operations.entries.deadline'))
                                                    ->dateTime()
                                                    ->icon('heroicon-o-clock')
                                                    ->placeholder('—'),

                                                Infolists\Components\TextEntry::make('productPackaging.name')
                                                    ->label(__('inventories::filament/clusters/operations/resources/operation.infolist.tabs.operations.entries.packaging'))
                                                    ->icon('heroicon-o-gift')
                                                    ->visible(fn (Settings\ProductSettings $settings) => $settings->enable_packagings)
                                                    ->placeholder('—'),

                                                Infolists\Components\TextEntry::make('product_qty')
                                                    ->label(__('inventories::filament/clusters/operations/resources/operation.infolist.tabs.operations.entries.demand'))
                                                    ->icon('heroicon-o-calculator'),

                                                Infolists\Components\TextEntry::make('quantity')
                                                    ->label(__('inventories::filament/clusters/operations/resources/operation.infolist.tabs.operations.entries.quantity'))
                                                    ->icon('heroicon-o-scale')
                                                    ->placeholder('—'),

                                                Infolists\Components\TextEntry::make('uom.name')
                                                    ->label(__('inventories::filament/clusters/operations/resources/operation.infolist.tabs.operations.entries.unit'))
                                                    ->icon('heroicon-o-beaker')
                                                    ->visible(fn (Settings\ProductSettings $settings) => $settings->enable_uom),

                                                Infolists\Components\IconEntry::make('is_picked')
                                                    ->label(__('inventories::filament/clusters/operations/resources/operation.infolist.tabs.operations.entries.picked'))
                                                    ->icon(fn (bool $state): string => $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                                                    ->color(fn (bool $state): string => $state ? 'success' : 'danger'),
                                            ]),
                                    ]),
                            ]),

                        Infolists\Components\Tabs\Tab::make(__('inventories::filament/clusters/operations/resources/operation.infolist.tabs.additional.title'))
                            ->schema([
                                Infolists\Components\Grid::make(2)
                                    ->schema([
                                        Infolists\Components\TextEntry::make('user.name')
                                            ->label(__('inventories::filament/clusters/operations/resources/operation.infolist.tabs.additional.entries.responsible'))
                                            ->icon('heroicon-o-user')
                                            ->placeholder('—'),

                                        Infolists\Components\TextEntry::make('move_type')
                                            ->label(__('inventories::filament/clusters/operations/resources/operation.infolist.tabs.additional.entries.shipping-policy'))
                                            ->icon('heroicon-o-truck')
                                            ->placeholder('—'),

                                        Infolists\Components\TextEntry::make('scheduled_at')
                                            ->label(__('inventories::filament/clusters/operations/resources/operation.infolist.tabs.additional.entries.scheduled-at'))
                                            ->dateTime()
                                            ->icon('heroicon-o-calendar')
                                            ->placeholder('—'),

                                        Infolists\Components\TextEntry::make('origin')
                                            ->label(__('inventories::filament/clusters/operations/resources/operation.infolist.tabs.additional.entries.source-document'))
                                            ->icon('heroicon-o-document-text')
                                            ->placeholder('—'),
                                    ]),
                            ]),

                        Infolists\Components\Tabs\Tab::make(__('inventories::filament/clusters/operations/resources/operation.infolist.tabs.note.title'))
                            ->schema([
                                Infolists\Components\TextEntry::make('description')
                                    ->markdown()
                                    ->hiddenLabel()
                                    ->placeholder('—'),
                            ]),
                    ]),
            ])
            ->columns(1);
    }

    /**
     * @param  array<mixed>  $parameters
     */
    public static function getUrl(string $name = 'index', array $parameters = [], bool $isAbsolute = true, ?string $panel = null, ?Model $tenant = null): string
    {
        return match ($parameters['record']?->operationType->type) {
            Enums\OperationType::INCOMING => Resources\ReceiptResource::getUrl('view', $parameters, $isAbsolute, $panel, $tenant),
            Enums\OperationType::INTERNAL => Resources\InternalResource::getUrl('view', $parameters, $isAbsolute, $panel, $tenant),
            Enums\OperationType::OUTGOING => Resources\DeliveryResource::getUrl('view', $parameters, $isAbsolute, $panel, $tenant),
            default                       => parent::getUrl('view', $parameters, $isAbsolute, $panel, $tenant),
        };
    }

    public static function getMovesRepeater(): Forms\Components\Repeater
    {
        return Forms\Components\Repeater::make('moves')
            ->hiddenLabel()
            ->relationship()
            ->schema([
                Forms\Components\Select::make('product_id')
                    ->label(__('inventories::filament/clusters/operations/resources/operation.form.tabs.operations.fields.product'))
                    ->relationship('product', 'name')
                    ->relationship(
                        'product',
                        'name',
                        fn ($query) => $query->where('type', ProductType::GOODS)->whereNull('is_configurable'),
                    )
                    ->required()
                    ->searchable()
                    ->preload()
                    ->distinct()
                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                    ->live()
                    ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get) {
                        static::afterProductUpdated($set, $get);
                    })
                    ->disabled(fn (Move $move): bool => $move->id && $move->state !== Enums\MoveState::DRAFT),
                Forms\Components\Select::make('final_location_id')
                    ->label(__('inventories::filament/clusters/operations/resources/operation.form.tabs.operations.fields.final-location'))
                    ->relationship('finalLocation', 'full_name')
                    ->searchable()
                    ->preload()
                    ->visible(fn (Settings\WarehouseSettings $settings) => $settings->enable_locations)
                    ->disabled(fn ($record): bool => in_array($record?->state, [Enums\MoveState::DONE, Enums\MoveState::CANCELED])),
                Forms\Components\TextInput::make('description')
                    ->label(__('inventories::filament/clusters/operations/resources/operation.form.tabs.operations.fields.description'))
                    ->disabled(fn ($record): bool => in_array($record?->state, [Enums\MoveState::DONE, Enums\MoveState::CANCELED])),
                Forms\Components\DateTimePicker::make('scheduled_at')
                    ->label(__('inventories::filament/clusters/operations/resources/operation.form.tabs.operations.fields.scheduled-at'))
                    ->default(now())
                    ->native(false)
                    ->disabled(fn ($record): bool => in_array($record?->state, [Enums\MoveState::DONE, Enums\MoveState::CANCELED])),
                Forms\Components\DateTimePicker::make('deadline')
                    ->label(__('inventories::filament/clusters/operations/resources/operation.form.tabs.operations.fields.deadline'))
                    ->native(false)
                    ->disabled(fn ($record): bool => in_array($record?->state, [Enums\MoveState::DONE, Enums\MoveState::CANCELED])),
                Forms\Components\Select::make('product_packaging_id')
                    ->label(__('inventories::filament/clusters/operations/resources/operation.form.tabs.operations.fields.packaging'))
                    ->relationship('productPackaging', 'name')
                    ->searchable()
                    ->preload()
                    ->visible(fn (Settings\ProductSettings $settings) => $settings->enable_packagings)
                    ->disabled(fn ($record): bool => in_array($record?->state, [Enums\MoveState::DONE, Enums\MoveState::CANCELED])),
                Forms\Components\TextInput::make('product_uom_qty')
                    ->label(__('inventories::filament/clusters/operations/resources/operation.form.tabs.operations.fields.demand'))
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get) {
                        static::afterProductUOMQtyUpdated($set, $get);
                    })
                    ->disabled(fn (Move $move): bool => $move->id && $move->state !== Enums\MoveState::DRAFT),
                Forms\Components\TextInput::make('quantity')
                    ->label(__('inventories::filament/clusters/operations/resources/operation.form.tabs.operations.fields.quantity'))
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->required()
                    ->visible(fn (Move $move): bool => $move->id && $move->state !== Enums\MoveState::DRAFT)
                    ->disabled(fn ($record): bool => in_array($record?->state, [Enums\MoveState::DONE, Enums\MoveState::CANCELED]))
                    ->suffixAction(fn ($record) => static::getMoveLinesAction($record)),
                Forms\Components\Select::make('uom_id')
                    ->label(__('inventories::filament/clusters/operations/resources/operation.form.tabs.operations.fields.unit'))
                    ->relationship(
                        'uom',
                        'name',
                        fn ($query) => $query->where('category_id', 1),
                    )
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get) {
                        static::afterUOMUpdated($set, $get);
                    })
                    ->visible(fn (Settings\ProductSettings $settings): bool => $settings->enable_uom)
                    ->disabled(fn ($record): bool => in_array($record?->state, [Enums\MoveState::DONE, Enums\MoveState::CANCELED])),
                Forms\Components\Toggle::make('is_picked')
                    ->label(__('inventories::filament/clusters/operations/resources/operation.form.tabs.operations.fields.picked'))
                    ->default(0)
                    ->inline(false)
                    ->disabled(fn ($record): bool => in_array($record?->state, [Enums\MoveState::DONE, Enums\MoveState::CANCELED])),
                Forms\Components\Hidden::make('product_qty')
                    ->default(0),
            ])
            ->columns(4)
            ->mutateRelationshipDataBeforeCreateUsing(function (array $data, $record) {
                $product = Product::find($data['product_id']);

                $data = array_merge($data, [
                    'creator_id'              => Auth::id(),
                    'company_id'              => Auth::user()->default_company_id,
                    'warehouse_id'            => $record->destinationLocation->warehouse_id,
                    'state'                   => $record->state->value,
                    'name'                    => $product->name,
                    'procure_method'          => Enums\ProcureMethod::MAKE_TO_STOCK,
                    'uom_id'                  => $data['uom_id'] ?? $product->uom_id,
                    'operation_type_id'       => $record->operation_type_id,
                    'quantity'                => null,
                    'source_location_id'      => $record->source_location_id,
                    'destination_location_id' => $record->destination_location_id,
                    'scheduled_at'            => $record->scheduled_at ?? now(),
                    'reference'               => $record->name,
                ]);

                return $data;
            })
            ->mutateRelationshipDataBeforeSaveUsing(function (array $data, $record) {
                if (isset($data['quantity'])) {
                    $record->fill([
                        'quantity' => $data['quantity'] ?? null,
                    ]);

                    Inventory::updateOrCreateMoveLines($record);

                    Inventory::computeTransferState($record->operation);
                }

                return $data;
            })
            ->deletable(fn ($record): bool => ! in_array($record?->state, [Enums\OperationState::DONE, Enums\OperationState::CANCELED]))
            ->addable(fn ($record): bool => ! in_array($record?->state, [Enums\OperationState::DONE, Enums\OperationState::CANCELED]));
    }

    public static function getMoveLinesAction($move): Forms\Components\Actions\Action
    {
        $columns = 2;

        if (
            app(Settings\TraceabilitySettings::class)->enable_lots_serial_numbers
            && (
                $move->product->tracking == Enums\ProductTracking::LOT
                || $move->product->tracking == Enums\ProductTracking::SERIAL
            )
            && $move->sourceLocation->type == Enums\LocationType::SUPPLIER
        ) {
            $columns++;
        }

        if ($move->sourceLocation->type == Enums\LocationType::INTERNAL) {
            $columns++;
        }

        if ($move->destinationLocation->type != Enums\LocationType::INTERNAL) {
            $columns--;
        }

        if (app(Settings\OperationSettings::class)->enable_packages) {
            $columns++;
        }

        return Forms\Components\Actions\Action::make('manageLines')
            ->icon('heroicon-m-bars-4')
            ->label(__('inventories::filament/clusters/operations/resources/operation.form.tabs.operations.fields.lines.modal-heading'))
            ->modalSubmitActionLabel('Save')
            ->visible(app(Settings\WarehouseSettings::class)->enable_locations)
            ->form([
                Forms\Components\Repeater::make('lines')
                    ->hiddenLabel()
                    ->relationship('lines')
                    ->schema([
                        Forms\Components\Select::make('quantity_id')
                            ->label(__(__('inventories::filament/clusters/operations/resources/operation.form.tabs.operations.fields.lines.fields.pick-from')))
                            ->options(function ($record) use ($move) {
                                if (in_array($record?->state, [Enums\MoveState::DONE, Enums\MoveState::CANCELED])) {
                                    $nameParts = array_filter([
                                        $record->sourceLocation->full_name,
                                        $record->lot?->name,
                                        $record->package?->name,
                                    ]);

                                    return [
                                        $record->id => implode(' - ', $nameParts),
                                    ];
                                }

                                return ProductQuantity::with(['location', 'lot', 'package'])
                                    ->where('product_id', $move->product_id)
                                    ->whereHas('location', function (Builder $query) use ($move) {
                                        $query->where('id', $move->source_location_id)
                                            ->orWhere('parent_id', $move->source_location_id);
                                    })
                                    ->get()
                                    ->mapWithKeys(function ($quantity) {
                                        $nameParts = array_filter([
                                            $quantity->location->full_name,
                                            $quantity->lot?->name,
                                            $quantity->package?->name,
                                        ]);

                                        return [$quantity->id => implode(' - ', $nameParts)];
                                    })
                                    ->toArray();
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateHydrated(function (Forms\Components\Select $component, $record) {
                                if (in_array($record?->state, [Enums\MoveState::DONE, Enums\MoveState::CANCELED])) {
                                    $component->state($record->id);

                                    return;
                                }

                                $productQuantity = ProductQuantity::with(['location', 'lot', 'package'])
                                    ->where('product_id', $record?->product_id)
                                    ->where('location_id', $record?->source_location_id)
                                    ->where('lot_id', $record?->lot_id ?? null)
                                    ->where('package_id', $record?->package_id ?? null)
                                    ->first();

                                $component->state($productQuantity?->id);
                            })
                            ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get) use ($move) {
                                $productQuantity = ProductQuantity::find($get('quantity_id'));

                                $set('lot_id', $productQuantity?->lot_id);

                                $set('result_package_id', $productQuantity?->package_id);

                                if ($productQuantity?->quantity) {
                                    $set('qty', static::calculateProductUOMQuantity($move->uom_id, $productQuantity->quantity));
                                }
                            })
                            ->visible($move->sourceLocation->type == Enums\LocationType::INTERNAL)
                            ->disabled(fn (): bool => in_array($move->state, [Enums\MoveState::DONE, Enums\MoveState::CANCELED])),
                        Forms\Components\Select::make('lot_id')
                            ->label(__('inventories::filament/clusters/operations/resources/operation.form.tabs.operations.fields.lines.fields.lot'))
                            ->relationship(
                                name: 'lot',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn (Builder $query) => $query->where('product_id', $move->product_id),
                            )
                            ->searchable()
                            ->preload()
                            ->required()
                            ->disabled(fn (): bool => in_array($move->state, [Enums\MoveState::DONE, Enums\MoveState::CANCELED]))
                            ->disableOptionWhen(fn () => ! $move->operationType->use_existing_lots)
                            ->createOptionForm(fn (Form $form): Form => LotResource::form($form))
                            ->createOptionAction(function (Action $action) use ($move) {
                                $action->visible($move->operationType->use_create_lots)
                                    ->mutateFormDataUsing(function (array $data) use ($move) {
                                        $data['product_id'] = $move->product_id;

                                        return $data;
                                    });
                            })
                            ->visible(fn (Settings\TraceabilitySettings $settings): bool => $settings->enable_lots_serial_numbers
                                && (
                                    $move->product->tracking == Enums\ProductTracking::LOT
                                    || $move->product->tracking == Enums\ProductTracking::SERIAL
                                )
                                && $move->sourceLocation->type == Enums\LocationType::SUPPLIER
                            ),
                        Forms\Components\Select::make('destination_location_id')
                            ->label(__('inventories::filament/clusters/operations/resources/operation.form.tabs.operations.fields.lines.fields.location'))
                            ->relationship(
                                name: 'destinationLocation',
                                titleAttribute: 'full_name',
                                modifyQueryUsing: fn (Builder $query) => $query
                                    ->where('type', '<>', Enums\LocationType::VIEW)
                                    ->where(function ($query) use ($move) {
                                        $query->where('id', $move->destination_location_id)
                                            ->orWhere('parent_id', $move->destination_location_id);
                                    }),
                            )
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->default($move->destination_location_id)
                            ->afterStateUpdated(function (Forms\Set $set) {
                                $set('result_package_id', null);
                            })
                            ->visible($move->destinationLocation->type == Enums\LocationType::INTERNAL)
                            ->disabled(fn (): bool => in_array($move->state, [Enums\MoveState::DONE, Enums\MoveState::CANCELED])),
                        Forms\Components\Select::make('result_package_id')
                            ->label(__('inventories::filament/clusters/operations/resources/operation.form.tabs.operations.fields.lines.fields.package'))
                            ->relationship(
                                name: 'resultPackage',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn (Builder $query, Forms\Get $get, $record) => $query
                                    ->where(function ($query) use ($get, $record) {
                                        $query->where('location_id', $get('destination_location_id'))
                                            ->orWhere('id', $record?->package_id)
                                            ->orWhereNull('location_id');
                                    }),
                            )
                            ->searchable()
                            ->preload()
                            ->createOptionForm(fn (Form $form): Form => PackageResource::form($form))
                            ->createOptionAction(function (Action $action) use ($move) {
                                $action->mutateFormDataUsing(function (array $data) use ($move) {
                                    $data['company_id'] = $move->company_id;

                                    return $data;
                                });
                            })
                            ->disabled(fn (): bool => in_array($move->state, [Enums\MoveState::DONE, Enums\MoveState::CANCELED]))
                            ->visible(fn (Settings\OperationSettings $settings) => $settings->enable_packages),
                        Forms\Components\TextInput::make('qty')
                            ->label(__('inventories::filament/clusters/operations/resources/operation.form.tabs.operations.fields.lines.fields.quantity'))
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(fn () => $move->product->tracking == Enums\ProductTracking::SERIAL ? 1 : 999999999)
                            ->required()
                            ->suffix(function () use ($move) {
                                if (! app(Settings\ProductSettings::class)->enable_uom) {
                                    return false;
                                }

                                return $move->uom->name;
                            })
                            ->disabled(fn (): bool => in_array($move->state, [Enums\MoveState::DONE, Enums\MoveState::CANCELED])),
                    ])
                    ->defaultItems(0)
                    ->addActionLabel(__('inventories::filament/clusters/operations/resources/operation.form.tabs.operations.fields.lines.add-line'))
                    ->columns($columns)
                    ->mutateRelationshipDataBeforeCreateUsing(function (array $data, Move $move): array {
                        if (isset($data['quantity_id'])) {
                            $productQuantity = ProductQuantity::find($data['quantity_id']);

                            $data['lot_id'] = $productQuantity?->lot_id;

                            $data['package_id'] = $productQuantity?->package_id;
                        }

                        $data['reference'] = $move->reference;
                        $data['state'] = $move->state;
                        $data['uom_qty'] = static::calculateProductQuantity($data['uom_id'] ?? $move->uom_id, $data['qty']);
                        $data['scheduled_at'] = $move->scheduled_at;
                        $data['operation_id'] = $move->operation_id;
                        $data['move_id'] = $move->id;
                        $data['source_location_id'] = $move->source_location_id;
                        $data['uom_id'] ??= $move->uom_id;
                        $data['creator_id'] = Auth::id();
                        $data['product_id'] = $move->product_id;
                        $data['company_id'] = $move->company_id;

                        return $data;
                    })
                    ->mutateRelationshipDataBeforeSaveUsing(function (array $data): array {
                        if (isset($data['quantity_id'])) {
                            $productQuantity = ProductQuantity::find($data['quantity_id']);

                            $data['lot_id'] = $productQuantity?->lot_id;

                            $data['package_id'] = $productQuantity?->package_id;
                        }

                        return $data;
                    })
                    ->deletable(fn (): bool => ! in_array($move->state, [Enums\MoveState::DONE, Enums\MoveState::CANCELED]))
                    ->addable(fn (): bool => ! in_array($move->state, [Enums\MoveState::DONE, Enums\MoveState::CANCELED])),
            ])
            ->modalWidth('6xl')
            ->mountUsing(function (Forms\ComponentContainer $form, Move $record) {
                $form->fill([]);
            })
            ->action(function (Forms\Set $set, array $data, Move $record): void {
                $totalQty = $record->lines()->sum('qty');

                $record->fill([
                    'quantity' => $totalQty,
                ]);

                Inventory::updateOrCreateMoveLines($record);

                $set('quantity', $totalQty);
            });
    }

    public static function getPresetTableViews(): array
    {
        return [
            'todo_receipts' => PresetView::make(__('inventories::filament/clusters/operations/resources/operation.tabs.todo'))
                ->favorite()
                ->icon('heroicon-s-clipboard-document-list')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNotIn('state', [Enums\OperationState::DONE, Enums\OperationState::CANCELED])),
            'my_receipts' => PresetView::make(__('inventories::filament/clusters/operations/resources/operation.tabs.my'))
                ->favorite()
                ->icon('heroicon-s-user')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('user_id', Auth::id())),
            'favorite_receipts' => PresetView::make(__('inventories::filament/clusters/operations/resources/operation.tabs.starred'))
                ->favorite()
                ->icon('heroicon-s-star')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_favorite', true)),
            'draft_receipts' => PresetView::make(__('inventories::filament/clusters/operations/resources/operation.tabs.draft'))
                ->favorite()
                ->icon('heroicon-s-pencil-square')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('state', Enums\OperationState::DRAFT)),
            'waiting_receipts' => PresetView::make(__('inventories::filament/clusters/operations/resources/operation.tabs.waiting'))
                ->favorite()
                ->icon('heroicon-s-clock')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('state', Enums\OperationState::CONFIRMED)),
            'ready_receipts' => PresetView::make(__('inventories::filament/clusters/operations/resources/operation.tabs.ready'))
                ->favorite()
                ->icon('heroicon-s-play-circle')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('state', Enums\OperationState::ASSIGNED)),
            'done_receipts' => PresetView::make(__('inventories::filament/clusters/operations/resources/operation.tabs.done'))
                ->favorite()
                ->icon('heroicon-s-check-circle')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('state', Enums\OperationState::DONE)),
            'canceled_receipts' => PresetView::make(__('inventories::filament/clusters/operations/resources/operation.tabs.canceled'))
                ->icon('heroicon-s-x-circle')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('state', Enums\OperationState::CANCELED)),
        ];
    }

    private static function afterProductUpdated(Forms\Set $set, Forms\Get $get): void
    {
        if (! $get('product_id')) {
            return;
        }

        $product = Product::find($get('product_id'));

        $set('uom_id', $product->uom_id);

        $productQuantity = static::calculateProductQuantity($get('uom_id'), $get('product_uom_qty'));

        $set('product_qty', round($productQuantity, 2));

        $packaging = static::getBestPackaging($get('product_id'), round($productQuantity, 2));

        $set('product_packaging_id', $packaging['packaging_id'] ?? null);
    }

    private static function afterProductUOMQtyUpdated(Forms\Set $set, Forms\Get $get): void
    {
        if (! $get('product_id')) {
            return;
        }

        $productQuantity = static::calculateProductQuantity($get('uom_id'), $get('product_uom_qty'));

        $set('product_qty', round($productQuantity, 2));

        $packaging = static::getBestPackaging($get('product_id'), $productQuantity);

        $set('product_packaging_id', $packaging['packaging_id'] ?? null);
    }

    private static function afterUOMUpdated(Forms\Set $set, Forms\Get $get): void
    {
        if (! $get('product_id')) {
            return;
        }

        $productQuantity = static::calculateProductQuantity($get('uom_id'), $get('product_uom_qty'));

        $set('product_qty', round($productQuantity, 2));

        $packaging = static::getBestPackaging($get('product_id'), $productQuantity);

        $set('product_packaging_id', $packaging['packaging_id'] ?? null);
    }

    public static function calculateProductQuantity($uomId, $uomQuantity)
    {
        if (! $uomId) {
            return $uomQuantity;
        }

        $uom = Uom::find($uomId);

        return (float) ($uomQuantity ?? 0) / $uom->factor;
    }

    public static function calculateProductUOMQuantity($uomId, $productQuantity)
    {
        if (! $uomId) {
            return $productQuantity;
        }

        $uom = Uom::find($uomId);

        return (float) ($productQuantity ?? 0) * $uom->factor;
    }

    private static function getBestPackaging($productId, $quantity)
    {
        $packagings = Packaging::where('product_id', $productId)
            ->orderByDesc('qty')
            ->get();

        foreach ($packagings as $packaging) {
            if ($quantity && $quantity % $packaging->qty == 0) {
                return [
                    'packaging_id'  => $packaging->id,
                    'packaging_qty' => round($quantity / $packaging->qty, 2),
                ];
            }
        }

        return null;
    }
}
