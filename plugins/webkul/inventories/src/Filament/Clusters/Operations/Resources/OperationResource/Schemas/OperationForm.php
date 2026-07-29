<?php

namespace Webkul\Inventory\Filament\Clusters\Operations\Resources\OperationResource\Schemas;

use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\View\Components\InputComponent\WrapperComponent\IconComponent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\ComponentAttributeBag;
use Webkul\Field\Filament\Forms\Components\ProgressStepper as FormProgressStepper;
use Webkul\Inventory\Enums;
use Webkul\Inventory\Enums\LocationType;
use Webkul\Inventory\Enums\MoveState;
use Webkul\Inventory\Enums\MoveType;
use Webkul\Inventory\Enums\OperationState;
use Webkul\Inventory\Enums\ProductTracking;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\OperationResource;
use Webkul\Inventory\Filament\Clusters\Products\Resources\PackageResource;
use Webkul\Inventory\Filament\Clusters\Products\Resources\ProductResource;
use Webkul\Inventory\Models\Lot;
use Webkul\Inventory\Models\Move;
use Webkul\Inventory\Models\Operation;
use Webkul\Inventory\Models\OperationType;
use Webkul\Inventory\Models\Packaging;
use Webkul\Inventory\Models\Product;
use Webkul\Inventory\Models\ProductQuantity;
use Webkul\Partner\Filament\Resources\PartnerResource;
use Webkul\Product\Enums\ProductType;
use Webkul\Support\Filament\Forms\Components\Repeater;
use Webkul\Support\Filament\Forms\Components\Repeater\TableColumn;
use Webkul\Support\Models\UOM;

class OperationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                FormProgressStepper::make('state')
                    ->hiddenLabel()
                    ->inline()
                    ->options(OperationState::options())
                    ->options(function ($record) {
                        $options = OperationState::options();

                        if (! $record) {
                            unset($options[OperationState::WAITING->value]);
                        } else {
                            if ($record->state !== OperationState::CANCELED) {
                                unset($options[OperationState::CANCELED->value]);
                            }

                            if ($record->state !== OperationState::WAITING) {
                                unset($options[OperationState::WAITING->value]);
                            }
                        }

                        return $options;
                    })
                    ->default(OperationState::DRAFT)
                    ->disabled(),
                Section::make(__('inventories::filament/clusters/operations/resources/operation.form.sections.general.title'))
                    ->schema([
                        Select::make('partner_id')
                            ->label(__('inventories::filament/clusters/operations/resources/operation.form.sections.general.fields.receive-from'))
                            ->relationship(
                                name: 'partner',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn (Builder $query) => $query->withTrashed()
                            )
                            ->getOptionLabelFromRecordUsing(function ($record): string {
                                return $record->name.($record->trashed() ? ' (Deleted)' : '');
                            })
                            ->disableOptionWhen(fn ($label) => str_contains($label, ' (Deleted)'))
                            ->searchable()
                            ->preload()
                            ->createOptionForm(fn (Schema $schema): Schema => PartnerResource::form($schema))
                            ->visible(fn (Get $get): bool => OperationType::withTrashed()->find($get('operation_type_id'))?->type == Enums\OperationType::INCOMING)
                            ->disabled(fn ($record): bool => in_array($record?->state, [OperationState::DONE, OperationState::CANCELED])),
                        Select::make('partner_id')
                            ->label(__('inventories::filament/clusters/operations/resources/operation.form.sections.general.fields.contact'))
                            ->relationship('partner', 'name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm(fn (Schema $schema): Schema => PartnerResource::form($schema))
                            ->visible(fn (Get $get): bool => OperationType::withTrashed()->find($get('operation_type_id'))?->type == Enums\OperationType::INTERNAL)
                            ->disabled(fn ($record): bool => in_array($record?->state, [OperationState::DONE, OperationState::CANCELED])),
                        Select::make('partner_id')
                            ->label(__('inventories::filament/clusters/operations/resources/operation.form.sections.general.fields.delivery-address'))
                            ->relationship('partner', 'name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm(fn (Schema $schema): Schema => PartnerResource::form($schema))
                            ->visible(fn (Get $get): bool => OperationType::withTrashed()->find($get('operation_type_id'))?->type == Enums\OperationType::OUTGOING)
                            ->disabled(fn ($record): bool => in_array($record?->state, [OperationState::DONE, OperationState::CANCELED])),
                        Select::make('operation_type_id')
                            ->label(__('inventories::filament/clusters/operations/resources/operation.form.sections.general.fields.operation-type'))
                            ->relationship(
                                name: 'operationType',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn (Builder $query) => $query->withTrashed()
                            )
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->getOptionLabelFromRecordUsing(function (OperationType $record) {
                                if (! $record->warehouse) {
                                    return $record->name;
                                }

                                return $record->warehouse->name.': '.$record->name.($record->trashed() ? ' (Deleted)' : '');
                            })
                            ->disableOptionWhen(function ($label) {
                                return str_contains($label, ' (Deleted)');
                            })
                            ->afterStateUpdated(function (Set $set, Get $get) {
                                $operationType = OperationType::withTrashed()->find($get('operation_type_id'));

                                $set('source_location_id', $operationType?->source_location_id);
                                $set('destination_location_id', $operationType?->destination_location_id);
                            })
                            ->disabled(fn ($record): bool => in_array($record?->state, [OperationState::DONE, OperationState::CANCELED])),
                        Select::make('source_location_id')
                            ->label(__('inventories::filament/clusters/operations/resources/operation.form.sections.general.fields.source-location'))
                            ->relationship(
                                'sourceLocation',
                                'full_name',
                                modifyQueryUsing: fn (Builder $query) => $query->withTrashed(),
                            )
                            ->getOptionLabelFromRecordUsing(function ($record): string {
                                return $record->full_name.($record->trashed() ? ' (Deleted)' : '');
                            })
                            ->disableOptionWhen(function ($label) {
                                return str_contains($label, ' (Deleted)');
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->visible(fn (Get $get): bool => OperationResource::getWarehouseSettings()->enable_locations && OperationType::withTrashed()->find($get('operation_type_id'))?->type != Enums\OperationType::INCOMING)
                            ->disabled(fn ($record): bool => in_array($record?->state, [OperationState::DONE, OperationState::CANCELED])),
                        Select::make('destination_location_id')
                            ->label(__('inventories::filament/clusters/operations/resources/operation.form.sections.general.fields.destination-location'))
                            ->relationship(
                                'destinationLocation',
                                'full_name',
                                modifyQueryUsing: fn (Builder $query) => $query->withTrashed(),
                            )
                            ->getOptionLabelFromRecordUsing(function ($record): string {
                                return $record->full_name.($record->trashed() ? ' (Deleted)' : '');
                            })
                            ->disableOptionWhen(function ($label) {
                                return str_contains($label, ' (Deleted)');
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Get $get, ?Operation $record): void {
                                $destinationLocationId = $get('destination_location_id');

                                if (
                                    ! $record?->id
                                    || ! $destinationLocationId
                                ) {
                                    return;
                                }

                                $record->update(['destination_location_id' => $destinationLocationId]);

                                $record->moves()->update(['destination_location_id' => $destinationLocationId]);

                                $record->moveLines()->update(['destination_location_id' => $destinationLocationId]);
                            })
                            ->visible(fn (Get $get): bool => OperationResource::getWarehouseSettings()->enable_locations && OperationType::withTrashed()->find($get('operation_type_id'))?->type != Enums\OperationType::OUTGOING)
                            ->disabled(fn ($record): bool => in_array($record?->state, [OperationState::DONE, OperationState::CANCELED])),
                    ])
                    ->columns(2),

                Tabs::make()
                    ->schema([
                        Tab::make(__('inventories::filament/clusters/operations/resources/operation.form.tabs.operations.title'))
                            ->schema([
                                static::getMovesRepeater(),
                            ]),

                        Tab::make(__('inventories::filament/clusters/operations/resources/operation.form.tabs.additional.title'))
                            ->schema([
                                Select::make('user_id')
                                    ->label(__('inventories::filament/clusters/operations/resources/operation.form.tabs.additional.fields.responsible'))
                                    ->relationship('user', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->default(Auth::id())
                                    ->disabled(fn ($record): bool => in_array($record?->state, [OperationState::DONE, OperationState::CANCELED])),
                                Select::make('move_type')
                                    ->label(__('inventories::filament/clusters/operations/resources/operation.form.tabs.additional.fields.shipping-policy'))
                                    ->options(MoveType::class)
                                    ->default(MoveType::DIRECT)
                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('inventories::filament/clusters/operations/resources/operation.form.tabs.additional.fields.shipping-policy-hint-tooltip'))
                                    ->visible(fn (Get $get): bool => OperationType::withTrashed()->find($get('operation_type_id'))?->type != Enums\OperationType::INCOMING)
                                    ->disabled(fn ($record): bool => in_array($record?->state, [OperationState::DONE, OperationState::CANCELED])),
                                DateTimePicker::make('scheduled_at')
                                    ->label(__('inventories::filament/clusters/operations/resources/operation.form.tabs.additional.fields.scheduled-at'))
                                    ->native(false)
                                    ->default(now()->format('Y-m-d H:i:s'))
                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('inventories::filament/clusters/operations/resources/operation.form.tabs.additional.fields.scheduled-at-hint-tooltip'))
                                    ->disabled(fn ($record): bool => in_array($record?->state, [OperationState::DONE, OperationState::CANCELED])),
                                TextInput::make('origin')
                                    ->label(__('inventories::filament/clusters/operations/resources/operation.form.tabs.additional.fields.source-document'))
                                    ->maxLength(255)
                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('inventories::filament/clusters/operations/resources/operation.form.tabs.additional.fields.source-document-hint-tooltip'))
                                    ->disabled(fn ($record): bool => in_array($record?->state, [OperationState::DONE, OperationState::CANCELED])),
                            ])
                            ->columns(2),

                        Tab::make(__('inventories::filament/clusters/operations/resources/operation.form.tabs.note.title'))
                            ->schema([
                                RichEditor::make('description')
                                    ->hiddenLabel(),
                            ]),
                    ]),
            ])
            ->columns(1);
    }

    public static function getMovesRepeater(): Repeater
    {
        return Repeater::make('moves')
            ->hiddenLabel()
            ->compact()
            ->relationship(
                modifyQueryUsing: fn (Builder $query) => $query->with([
                    'product' => fn ($q) => $q->withTrashed(),
                    'finalLocation',
                    'uom',
                    'productPackaging',
                ])
            )
            ->mutateRelationshipDataBeforeSaveUsing(function (array $data, $record, $livewire) {
                $data['source_location_id'] = $livewire->data['source_location_id']
                    ?? $record->operationType?->source_location_id;

                $data['destination_location_id'] = $livewire->data['destination_location_id']
                    ?? $record->operationType?->destination_location_id;

                return $data;
            })
            ->mutateRelationshipDataBeforeCreateUsing(function (array $data, $record, $livewire) {
                Move::$globalContext['skip_additional'] = false;

                $data['source_location_id'] = $livewire->data['source_location_id']
                    ?? $record->operationType?->source_location_id;

                $data['destination_location_id'] = $livewire->data['destination_location_id']
                    ?? $record->operationType?->destination_location_id;

                return $data;
            })
            ->columnManagerColumns(2)
            ->table(fn ($record) => [
                TableColumn::make('product_id')
                    ->label(__('inventories::filament/clusters/operations/resources/operation.form.tabs.operations.columns.product'))
                    ->width(300)
                    ->resizable()
                    ->markAsRequired(),
                TableColumn::make('final_location_id')
                    ->label(__('inventories::filament/clusters/operations/resources/operation.form.tabs.operations.columns.final-location'))
                    ->resizable()
                    ->visible(OperationResource::getWarehouseSettings()->enable_locations)
                    ->toggleable(isToggledHiddenByDefault: true),
                TableColumn::make('description_picking')
                    ->label(__('inventories::filament/clusters/operations/resources/operation.form.tabs.operations.columns.description'))
                    ->resizable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TableColumn::make('scheduled_at')
                    ->label(__('inventories::filament/clusters/operations/resources/operation.form.tabs.operations.columns.scheduled-at'))
                    ->resizable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TableColumn::make('deadline')
                    ->label(__('inventories::filament/clusters/operations/resources/operation.form.tabs.operations.columns.deadline'))
                    ->resizable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TableColumn::make('product_packaging_id')
                    ->label(__('inventories::filament/clusters/operations/resources/operation.form.tabs.operations.columns.packaging'))
                    ->resizable()
                    ->visible(OperationResource::getProductSettings()->enable_packagings),
                TableColumn::make('product_uom_qty')
                    ->label(__('inventories::filament/clusters/operations/resources/operation.form.tabs.operations.columns.demand'))
                    ->resizable()
                    ->markAsRequired(),
                TableColumn::make('quantity')
                    ->label(__('inventories::filament/clusters/operations/resources/operation.form.tabs.operations.columns.quantity'))
                    ->resizable()
                    ->markAsRequired()
                    ->visible(fn () => $record?->moves->contains(fn ($move) => $move->id && $move->state !== MoveState::DRAFT)),
                TableColumn::make('uom_id')
                    ->label(__('inventories::filament/clusters/operations/resources/operation.form.tabs.operations.columns.unit'))
                    ->resizable()
                    ->markAsRequired()
                    ->visible(OperationResource::getProductSettings()->enable_uom),
                TableColumn::make('is_picked')
                    ->label(__('inventories::filament/clusters/operations/resources/operation.form.tabs.operations.columns.picked'))
                    ->resizable()
                    ->toggleable(),
            ])
            ->schema([
                Select::make('product_id')
                    ->label(__('inventories::filament/clusters/operations/resources/operation.form.tabs.operations.fields.product'))
                    ->relationship(
                        name: 'product',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn (Builder $query) => $query
                            ->withTrashed()
                            ->where('type', ProductType::GOODS)
                            ->whereNull('is_configurable'),
                    )
                    ->required()
                    ->searchable()
                    ->preload()
                    ->wrapOptionLabels(false)
                    ->getOptionLabelFromRecordUsing(function ($record): string {
                        return $record->name.($record->trashed() ? ' (Deleted)' : '');
                    })
                    ->disableOptionWhen(function ($value, $state, $component, $label) {
                        if (str_contains($label, ' (Deleted)')) {
                            return true;
                        }

                        $repeater = $component->getParentRepeater();

                        if (! $repeater) {
                            return false;
                        }

                        return collect($repeater->getState())
                            ->pluck(
                                (string) str($component->getStatePath())
                                    ->after("{$repeater->getStatePath()}.")
                                    ->after('.'),
                            )
                            ->flatten()
                            ->diff(Arr::wrap($state))
                            ->filter(fn (mixed $siblingItemState): bool => filled($siblingItemState))
                            ->contains($value);
                    })
                    ->distinct()
                    ->live()
                    ->afterStateUpdated(fn (Set $set, Get $get) => static::afterProductUpdated($set, $get))
                    ->disabled(fn (?Move $record): bool => $record?->id && $record?->state !== MoveState::DRAFT),
                Select::make('final_location_id')
                    ->label(__('inventories::filament/clusters/operations/resources/operation.form.tabs.operations.fields.final-location'))
                    ->relationship(
                        'finalLocation',
                        'full_name',
                        modifyQueryUsing: fn (Builder $query) => $query->withTrashed(),
                    )
                    ->getOptionLabelFromRecordUsing(function ($record): string {
                        return $record->full_name.($record->trashed() ? ' (Deleted)' : '');
                    })
                    ->disableOptionWhen(function ($label) {
                        return str_contains($label, ' (Deleted)');
                    })
                    ->searchable()
                    ->preload()
                    ->wrapOptionLabels(false)
                    ->visible(OperationResource::getWarehouseSettings()->enable_locations)
                    ->disabled(fn ($record): bool => in_array($record?->state, [MoveState::DONE, MoveState::CANCELED])),
                TextInput::make('description_picking')
                    ->label(__('inventories::filament/clusters/operations/resources/operation.form.tabs.operations.fields.description'))
                    ->maxLength(255)
                    ->disabled(fn ($record): bool => in_array($record?->state, [MoveState::DONE, MoveState::CANCELED])),
                DateTimePicker::make('scheduled_at')
                    ->label(__('inventories::filament/clusters/operations/resources/operation.form.tabs.operations.fields.scheduled-at'))
                    ->default(now())
                    ->suffixIcon('heroicon-o-calendar')
                    ->native(false)
                    ->disabled(fn ($record): bool => in_array($record?->state, [MoveState::DONE, MoveState::CANCELED])),
                DateTimePicker::make('deadline')
                    ->label(__('inventories::filament/clusters/operations/resources/operation.form.tabs.operations.fields.deadline'))
                    ->native(false)
                    ->suffixIcon('heroicon-o-calendar')
                    ->disabled(fn ($record): bool => in_array($record?->state, [MoveState::DONE, MoveState::CANCELED])),
                Select::make('product_packaging_id')
                    ->label(__('inventories::filament/clusters/operations/resources/operation.form.tabs.operations.fields.packaging'))
                    ->relationship(
                        'productPackaging',
                        'name',
                        modifyQueryUsing: fn (Builder $query, Get $get) => $query->where('product_id', $get('product_id')),
                    )
                    ->searchable()
                    ->preload()
                    ->wrapOptionLabels(false)
                    ->visible(OperationResource::getProductSettings()->enable_packagings)
                    ->disabled(fn ($record): bool => in_array($record?->state, [MoveState::DONE, MoveState::CANCELED])),
                TextInput::make('product_uom_qty')
                    ->label(__('inventories::filament/clusters/operations/resources/operation.form.tabs.operations.fields.demand'))
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(99999999999)
                    ->default(0)
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Set $set, Get $get) => static::afterProductUOMQtyUpdated($set, $get))
                    ->suffix(function (?Move $record, Get $get): mixed {
                        if (
                            ! $get('product_id')
                            || (float) ($get('product_uom_qty') ?? 0) <= 0
                            || (float) ($get('quantity') ?? 0) > 0
                            || ($record?->forecast_availability ?? 1) > 0
                            || ($record?->operationType?->type === Enums\OperationType::OUTGOING && $record?->state !== MoveState::DRAFT)
                        ) {
                            return null;
                        }

                        return \Filament\Support\generate_icon_html(
                            'heroicon-o-exclamation-triangle',
                            null,
                            (new ComponentAttributeBag)
                                ->color(IconComponent::class, 'warning')
                                ->class(['fi-text-color-600'])
                                ->merge([
                                    'style'         => 'color: var(--text)',
                                    'x-tooltip.raw' => __('inventories::filament/clusters/operations/resources/operation.form.tabs.operations.columns.insufficient-stock-tooltip'),
                                ], escape: false),
                        );
                    })
                    ->disabled(fn (?Move $record): bool => $record?->id && $record?->state !== MoveState::DRAFT),
                TextInput::make('quantity')
                    ->label(__('inventories::filament/clusters/operations/resources/operation.form.tabs.operations.fields.quantity'))
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(99999999999)
                    ->default(0)
                    ->required()
                    ->visible(fn (?Move $record): bool => ! $record?->id || $record?->state !== MoveState::DRAFT)
                    ->disabled(fn (?Move $record): bool => ! $record?->id || in_array($record?->state, [MoveState::DONE, MoveState::CANCELED]))
                    ->suffixAction(fn (?Move $record) => $record?->id ? static::getMoveLinesAction($record) : null),
                Select::make('uom_id')
                    ->label(__('inventories::filament/clusters/operations/resources/operation.form.tabs.operations.fields.unit'))
                    ->relationship(
                        'uom',
                        'name',
                        function (Builder $query, Get $get) {
                            $product = Product::find($get('product_id'));
                            $categoryId = $product?->uom?->category_id;

                            return $query->when($categoryId, fn ($q) => $q->where('category_id', $categoryId))->orderBy('id');
                        },
                    )
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live()
                    ->native(false)
                    ->wrapOptionLabels(false)
                    ->afterStateUpdated(function (Set $set, Get $get) {
                        static::afterUOMUpdated($set, $get);
                    })
                    ->visible(OperationResource::getProductSettings()->enable_uom)
                    ->disabled(fn ($record): bool => in_array($record?->state, [MoveState::DONE, MoveState::CANCELED])),
                Toggle::make('is_picked')
                    ->label(__('inventories::filament/clusters/operations/resources/operation.form.tabs.operations.fields.picked'))
                    ->default(0)
                    ->inline(false)
                    ->disabled(fn ($record): bool => in_array($record?->state, [MoveState::DONE, MoveState::CANCELED])),
                Hidden::make('product_qty')
                    ->default(0),
            ])
            ->columns(4)
            ->extraItemActions([
                Action::make('openProduct')
                    ->tooltip(__('inventories::filament/clusters/operations/resources/operation.form.tabs.operations.actions.open-product.tooltip'))
                    ->icon('heroicon-m-arrow-top-right-on-square')
                    ->url(
                        fn (array $arguments, Get $get): ?string => ProductResource::getUrl('edit', [
                            'record' => $get("moves.{$arguments['item']}.product_id"),
                        ])
                    )
                    ->openUrlInNewTab()
                    ->visible(
                        fn (array $arguments, Get $get): bool => filled($get("moves.{$arguments['item']}.product_id"))
                    ),
            ])
            ->deletable(fn ($record): bool => ! in_array($record?->state, [OperationState::DONE, OperationState::CANCELED]))
            ->addable(fn ($record): bool => ! in_array($record?->state, [OperationState::DONE, OperationState::CANCELED]));
    }

    public static function getMoveLinesAction(Move $move): Action
    {
        return Action::make('manageLines')
            ->icon('heroicon-m-bars-4')
            ->label(__('inventories::filament/clusters/operations/resources/operation.form.tabs.operations.fields.lines.modal-heading'))
            ->modalSubmitActionLabel(__('inventories::filament/clusters/operations/resources/operation.form.tabs.operations.fields.lines.modal-submit-action-label'))
            ->visible(OperationResource::getWarehouseSettings()->enable_locations)
            ->schema([
                Actions::make([
                    Action::make('generateLots')
                        ->label(__('inventories::filament/clusters/operations/resources/operation.form.tabs.operations.fields.lines.actions.generate'))
                        ->icon('heroicon-m-cog-6-tooth')
                        ->link()
                        ->schema([
                            TextInput::make('first_lot')
                                ->label(__('inventories::filament/clusters/operations/resources/operation.form.tabs.operations.fields.lines.fields.first-lot'))
                                ->required(),
                            TextInput::make('quantity_per_lot')
                                ->label(__('inventories::filament/clusters/operations/resources/operation.form.tabs.operations.fields.lines.fields.quantity-per-lot'))
                                ->numeric()
                                ->minValue(1)
                                ->default(fn () => $move->product->tracking == ProductTracking::SERIAL ? 1 : (int) ceil((float) ($move->product_uom_qty ?? 1)))
                                ->disabled($move->product->tracking == ProductTracking::SERIAL)
                                ->dehydrated(),
                            TextInput::make('quantity_received')
                                ->label(__('inventories::filament/clusters/operations/resources/operation.form.tabs.operations.fields.lines.fields.quantity-received'))
                                ->numeric()
                                ->minValue(1)
                                ->default(fn () => (int) ceil((float) ($move->product_uom_qty ?? 0)))
                                ->required(),
                            Toggle::make('keep_current_lines')
                                ->label(__('inventories::filament/clusters/operations/resources/operation.form.tabs.operations.fields.lines.fields.keep-current-lines'))
                                ->default(false),
                        ])
                        ->action(function (array $data, Get $get, Set $set) use ($move) {
                            $isSerial = $move->product->tracking == ProductTracking::SERIAL;

                            $perLot = $isSerial ? 1.0 : max(1.0, (float) ($data['quantity_per_lot'] ?? 1));

                            $total = (float) ($data['quantity_received'] ?? 0);

                            $count = $isSerial ? (int) $total : (int) ceil($total / $perLot);

                            if ($count < 1) {
                                return;
                            }

                            $names = array_column((new Lot)->generateLotNames($data['first_lot'], $count), 'lot_name');

                            $rows = static::buildGeneratedLineRows($move, $names, $perLot, $total);

                            if (! empty($data['keep_current_lines'])) {
                                $rows = ($get('lines') ?? []) + $rows;
                            }

                            $set('lines', $rows);
                        }),
                    Action::make('importLots')
                        ->label(__('inventories::filament/clusters/operations/resources/operation.form.tabs.operations.fields.lines.actions.import'))
                        ->icon('heroicon-m-arrow-up-tray')
                        ->link()
                        ->schema([
                            Textarea::make('serials')
                                ->label(__('inventories::filament/clusters/operations/resources/operation.form.tabs.operations.fields.lines.fields.serials'))
                                ->helperText(__('inventories::filament/clusters/operations/resources/operation.form.tabs.operations.fields.lines.fields.serials-helper'))
                                ->rows(6)
                                ->required(),
                            Toggle::make('keep_current_lines')
                                ->label(__('inventories::filament/clusters/operations/resources/operation.form.tabs.operations.fields.lines.fields.keep-current-lines'))
                                ->default(false),
                        ])
                        ->action(function (array $data, Get $get, Set $set) use ($move) {
                            $names = collect(preg_split('/\r\n|\r|\n/', (string) $data['serials']))
                                ->map(fn ($name) => trim($name))
                                ->filter()
                                ->values()
                                ->all();

                            if (empty($names)) {
                                return;
                            }

                            $rows = static::buildGeneratedLineRows($move, $names, 1.0, (float) count($names));

                            if (! empty($data['keep_current_lines'])) {
                                $rows = ($get('lines') ?? []) + $rows;
                            }

                            $set('lines', $rows);
                        }),
                ])
                    ->visible(
                        OperationResource::getTraceabilitySettings()->enable_lots_serial_numbers
                            && (
                                $move->product->tracking == ProductTracking::LOT
                                || $move->product->tracking == ProductTracking::SERIAL
                            )
                            && $move->sourceLocation->type == LocationType::SUPPLIER
                            && $move->operationType->use_create_lots
                            && ! in_array($move->state, [MoveState::DONE, MoveState::CANCELED])
                    ),
                Repeater::make('lines')
                    ->hiddenLabel()
                    ->compact()
                    ->relationship('lines')
                    ->table(fn () => [
                        TableColumn::make('quantity_id')
                            ->label(__('inventories::filament/clusters/operations/resources/operation.form.tabs.operations.fields.lines.fields.pick-from'))
                            ->markAsRequired()
                            ->visible($move->sourceLocation->type == LocationType::INTERNAL && $move->product->is_storable),
                        TableColumn::make('lot_name')
                            ->label(__('inventories::filament/clusters/operations/resources/operation.form.tabs.operations.fields.lines.fields.lot'))
                            ->markAsRequired()
                            ->visible(
                                OperationResource::getTraceabilitySettings()->enable_lots_serial_numbers
                                    && (
                                        $move->product->tracking == ProductTracking::LOT
                                        || $move->product->tracking == ProductTracking::SERIAL
                                    )
                                    && $move->sourceLocation->type == LocationType::SUPPLIER
                                    && (
                                        $move->operationType->use_create_lots
                                        || $move->operationType->use_existing_lots
                                    )
                            ),
                        TableColumn::make('destination_location_id')
                            ->label(__('inventories::filament/clusters/operations/resources/operation.form.tabs.operations.fields.lines.fields.location'))
                            ->markAsRequired(),
                        TableColumn::make('result_package_id')
                            ->label(__('inventories::filament/clusters/operations/resources/operation.form.tabs.operations.fields.lines.fields.package'))
                            ->visible(OperationResource::getOperationSettings()->enable_packages),
                        TableColumn::make('qty')
                            ->label(__('inventories::filament/clusters/operations/resources/operation.form.tabs.operations.fields.lines.fields.quantity'))
                            ->markAsRequired(),
                    ])
                    ->schema([
                        Select::make('quantity_id')
                            ->label(__(__('inventories::filament/clusters/operations/resources/operation.form.tabs.operations.fields.lines.fields.pick-from')))
                            ->options(function ($record) use ($move) {
                                if (in_array($record?->state, [MoveState::DONE, MoveState::CANCELED])) {
                                    $nameParts = array_filter([
                                        $record->sourceLocation->full_name,
                                        $record->lot?->name,
                                        $record->package?->name,
                                    ]);

                                    return [
                                        $record->id => implode(' - ', $nameParts),
                                    ];
                                }

                                [$quantityLocationScope] = $move->product->getLocationFilters();

                                return ProductQuantity::with(['location', 'lot', 'package'])
                                    ->where('product_id', $move->product_id)
                                    ->whereHas('location', function (Builder $query) use ($move) {
                                        $query->where('id', $move->source_location_id)
                                            ->orWhere('parent_id', $move->source_location_id);
                                    })
                                    // ->where('quantity', '>', 0)
                                    ->where(fn (Builder $query) => $quantityLocationScope($query))
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
                            ->afterStateHydrated(function (Select $component, $record) {
                                if (in_array($record?->state, [MoveState::DONE, MoveState::CANCELED])) {
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
                            ->afterStateUpdated(function (Set $set, Get $get) use ($move) {
                                $productQuantity = ProductQuantity::find($get('quantity_id'));

                                $set('lot_id', $productQuantity?->lot_id);

                                $set('result_package_id', $productQuantity?->package_id);

                                if ($productQuantity?->quantity) {
                                    if (! $move->uom_id) {
                                        $set('qty', $productQuantity->quantity);
                                    } else {
                                        $set('qty', (float) ($productQuantity->quantity ?? 0) * $move->uom->factor);
                                    }
                                }
                            })
                            ->visible($move->sourceLocation->type == LocationType::INTERNAL)
                            ->disabled(fn (): bool => in_array($move->state, [MoveState::DONE, MoveState::CANCELED])),
                        Select::make('lot_name')
                            ->label(__('inventories::filament/clusters/operations/resources/operation.form.tabs.operations.fields.lines.fields.lot'))
                            ->options(fn (): array => $move->operationType->use_existing_lots
                                ? Lot::query()
                                    ->where('product_id', $move->product_id)
                                    ->orderBy('name')
                                    ->pluck('name', 'name')
                                    ->all()
                                : [])
                            ->searchable()
                            ->getOptionLabelUsing(fn ($value): ?string => $value)
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->label(__('inventories::filament/clusters/operations/resources/operation.form.tabs.operations.fields.lines.fields.lot'))
                                    ->required(),
                            ])
                            ->createOptionUsing(fn (array $data): string => $data['name'])
                            ->createOptionAction(fn (Action $action) => $action->visible($move->operationType->use_create_lots))
                            ->required()
                            ->disabled(fn (): bool => in_array($move->state, [MoveState::DONE, MoveState::CANCELED]))
                            ->afterStateHydrated(function (Select $component, $state, $record) {
                                if (blank($state) && $record?->lot_id) {
                                    $component->state($record->lot?->name);
                                }
                            })
                            ->visible(
                                OperationResource::getTraceabilitySettings()->enable_lots_serial_numbers
                                    && (
                                        $move->product->tracking == ProductTracking::LOT
                                        || $move->product->tracking == ProductTracking::SERIAL
                                    )
                                    && $move->sourceLocation->type == LocationType::SUPPLIER
                                    && (
                                        $move->operationType->use_create_lots
                                        || $move->operationType->use_existing_lots
                                    )
                            ),
                        Select::make('destination_location_id')
                            ->label(__('inventories::filament/clusters/operations/resources/operation.form.tabs.operations.fields.lines.fields.location'))
                            ->relationship(
                                name: 'destinationLocation',
                                titleAttribute: 'full_name',
                                modifyQueryUsing: fn (Builder $query) => $query
                                    ->withTrashed()
                                    ->where(function ($query) use ($move) {
                                        $query->where('id', $move->destination_location_id)
                                            ->orWhere('parent_id', $move->destination_location_id);
                                    })
                            )
                            ->getOptionLabelFromRecordUsing(function ($record): string {
                                return $record->full_name.($record->trashed() ? ' (Deleted)' : '');
                            })
                            ->disableOptionWhen(function ($label) {
                                return str_contains($label, ' (Deleted)');
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Set $set) {
                                $set('result_package_id', null);
                            })
                            ->disabled(fn (): bool => in_array($move->state, [MoveState::DONE, MoveState::CANCELED])),
                        Select::make('result_package_id')
                            ->label(__('inventories::filament/clusters/operations/resources/operation.form.tabs.operations.fields.lines.fields.package'))
                            ->relationship(
                                name: 'resultPackage',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn (Builder $query, Get $get, $record) => $query
                                    ->where(function ($query) use ($get, $record) {
                                        $query->where('location_id', $get('destination_location_id'))
                                            ->orWhere('id', $record?->result_package_id ?? $get('result_package_id'))
                                            ->orWhereNull('location_id');
                                    }),
                            )
                            ->searchable()
                            ->preload()
                            ->createOptionForm(fn (Schema $schema): Schema => PackageResource::form($schema))
                            ->disabled(fn (): bool => in_array($move->state, [MoveState::DONE, MoveState::CANCELED]))
                            ->visible(OperationResource::getOperationSettings()->enable_packages),
                        TextInput::make('qty')
                            ->label(__('inventories::filament/clusters/operations/resources/operation.form.tabs.operations.fields.lines.fields.quantity'))
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(99999999999)
                            ->maxValue(fn () => $move->product->tracking == ProductTracking::SERIAL ? 1 : 999999999)
                            ->required()
                            ->suffix(function () use ($move) {
                                if (! OperationResource::getProductSettings()->enable_uom) {
                                    return false;
                                }

                                return $move->uom->name;
                            })
                            ->disabled(fn (): bool => in_array($move->state, [MoveState::DONE, MoveState::CANCELED])),
                    ])
                    ->defaultItems(0)
                    ->addActionLabel(__('inventories::filament/clusters/operations/resources/operation.form.tabs.operations.fields.lines.add-line'))
                    ->mutateRelationshipDataBeforeCreateUsing(function (array $data, Move $move): array {
                        if (isset($data['quantity_id'])) {
                            $productQuantity = ProductQuantity::find($data['quantity_id']);

                            $data['lot_id'] = $productQuantity?->lot_id;

                            $data['package_id'] = $productQuantity?->package_id;
                        }

                        $data['uom_qty'] = static::calculateProductQuantity($data['uom_id'] ?? $move->uom_id, $data['qty']);
                        $data['move_id'] = $move->id;

                        return $data;
                    })
                    ->mutateRelationshipDataBeforeSaveUsing(function (array $data) use ($move): array {
                        if (isset($data['quantity_id'])) {
                            $productQuantity = ProductQuantity::find($data['quantity_id']);

                            $data['lot_id'] = $productQuantity?->lot_id;

                            $data['package_id'] = $productQuantity?->package_id;
                        }

                        if (isset($data['qty'])) {
                            $data['uom_qty'] = static::calculateProductQuantity($data['uom_id'] ?? $move->uom_id, $data['qty']);
                        }

                        return $data;
                    })
                    ->deletable(fn (): bool => ! in_array($move->state, [MoveState::DONE, MoveState::CANCELED]))
                    ->addable(fn (): bool => ! in_array($move->state, [MoveState::DONE, MoveState::CANCELED])),
            ])
            ->modalWidth('6xl')
            ->mountUsing(function (Schema $schema) {
                $schema->fill([]);
            })
            ->modalSubmitAction(
                fn ($action) => $action
                    ->visible(! in_array($move->state, [MoveState::DONE, MoveState::CANCELED]))
            )
            ->databaseTransaction()
            ->action(function (Set $set, Move $move, Schema $schema): void {
                $schema->saveRelationships();

                $move->refresh();

                $set('quantity', $move->quantity);
            });
    }

    protected static function buildGeneratedLineRows(Move $move, array $names, float $qtyPerLot, float $qtyTotal): array
    {
        $isSerial = $move->product->tracking == ProductTracking::SERIAL;

        $names = array_values($names);

        $count = count($names);

        $rows = [];

        foreach ($names as $i => $name) {
            if ($isSerial) {
                $qty = 1.0;
            } elseif ($i === $count - 1) {
                $qty = $qtyTotal - ($qtyPerLot * ($count - 1));
            } else {
                $qty = $qtyPerLot;
            }

            $rows[(string) Str::uuid()] = [
                'quantity_id'             => null,
                'lot_name'                => $name,
                'lot_id'                  => null,
                'destination_location_id' => $move->destination_location_id,
                'result_package_id'       => null,
                'qty'                     => $qty,
            ];
        }

        return $rows;
    }

    private static function afterProductUpdated(Set $set, Get $get): void
    {
        if (! $get('product_id')) {
            return;
        }

        $product = Product::find($get('product_id'));

        $set('uom_id', $product->uom_id);

        $productQuantity = static::calculateProductQuantity($product->uom_id, $get('product_uom_qty'));

        $set('product_qty', round($productQuantity, 2));

        $packaging = static::getBestPackaging($get('product_id'), round($productQuantity, 2));

        $set('product_packaging_id', $packaging['packaging_id'] ?? null);
    }

    private static function afterProductUOMQtyUpdated(Set $set, Get $get): void
    {
        if (! $get('product_id')) {
            return;
        }

        $productQuantity = static::calculateProductQuantity($get('uom_id'), $get('product_uom_qty'));

        $set('product_qty', round($productQuantity, 2));

        $packaging = static::getBestPackaging($get('product_id'), $productQuantity);

        $set('product_packaging_id', $packaging['packaging_id'] ?? null);
    }

    private static function afterUOMUpdated(Set $set, Get $get): void
    {
        if (! $get('product_id')) {
            return;
        }

        $product = Product::find($get('product_id'));

        $selectedUom = UOM::find($get('uom_id'));

        if ($product?->uom && $selectedUom && $selectedUom->factor > $product->uom->factor) {
            Notification::make()
                ->title(__('inventories::filament/clusters/operations/resources/operation.notifications.uom-precision-warning.title'))
                ->body(__('inventories::filament/clusters/operations/resources/operation.notifications.uom-precision-warning.body'))
                ->warning()
                ->send();
        }

        $productQuantity = static::calculateProductQuantity($get('uom_id'), $get('product_uom_qty'));

        $set('product_qty', round($productQuantity, 2));

        $packaging = static::getBestPackaging($get('product_id'), $productQuantity);

        $set('product_packaging_id', $packaging['packaging_id'] ?? null);
    }

    public static function calculateProductQuantity($uomId, $uomQuantity)
    {
        if (! $uomId) {
            return self::normalizeZero((float) ($uomQuantity ?? 0));
        }

        $uom = UOM::find($uomId);

        if (! $uom || ! is_numeric($uom->factor) || $uom->factor == 0) {
            return 0;
        }

        $referenceUom = UOM::where('category_id', $uom->category_id)->where('factor', 1)->first();

        if (! $referenceUom) {
            return self::normalizeZero((float) ($uomQuantity ?? 0) / $uom->factor);
        }

        $quantity = $uom->computeQuantity((float) ($uomQuantity ?? 0), $referenceUom, false);

        return self::normalizeZero($quantity);
    }

    protected static function normalizeZero(float $value): float
    {
        return $value == 0 ? 0.0 : $value; // convert -0.0 to 0.0
    }

    private static function getBestPackaging($productId, $quantity)
    {
        $product = Product::find($productId);

        $packagings = Packaging::where('product_id', $product?->id)
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
