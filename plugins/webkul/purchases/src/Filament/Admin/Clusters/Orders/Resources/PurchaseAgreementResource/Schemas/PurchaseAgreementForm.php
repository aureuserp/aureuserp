<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseAgreementResource\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Webkul\Field\Filament\Forms\Components\ProgressStepper as FormProgressStepper;
use Webkul\Product\Enums\ProductType;
use Webkul\Product\Models\Product;
use Webkul\Purchase\Enums\RequisitionState;
use Webkul\Purchase\Enums\RequisitionType;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseAgreementResource;
use Webkul\Support\Filament\Forms\Components\Repeater;
use Webkul\Support\Filament\Forms\Components\Repeater\TableColumn;
use Webkul\Support\Models\Company;

class PurchaseAgreementForm
{
    public static function configure(Schema $schema, array $customFormFields = []): Schema
    {
        return $schema
            ->components([
                FormProgressStepper::make('state')
                    ->hiddenLabel()
                    ->inline()
                    ->options(RequisitionState::options())
                    ->default(RequisitionState::DRAFT)
                    ->disabled(),
                Section::make(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.form.sections.general.title'))
                    ->schema([
                        Group::make()
                            ->schema([
                                Select::make('partner_id')
                                    ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.form.sections.general.fields.vendor'))
                                    ->relationship(
                                        'partner',
                                        'name',
                                        fn (Builder $query) => $query->withTrashed()
                                    )
                                    ->getOptionLabelFromRecordUsing(function ($record): string {
                                        return $record->name.($record->trashed() ? ' (Deleted)' : '');
                                    })
                                    ->disableOptionWhen(fn ($label) => str_contains($label, ' (Deleted)'))
                                    ->searchable()
                                    ->required()
                                    ->preload()
                                    ->disabled(fn ($record): bool => $record && $record?->state != RequisitionState::DRAFT),
                                Select::make('user_id')
                                    ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.form.sections.general.fields.buyer'))
                                    ->relationship('user', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->disabled(fn ($record): bool => $record && $record?->state != RequisitionState::DRAFT),
                                Select::make('type')
                                    ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.form.sections.general.fields.agreement-type'))
                                    ->options(RequisitionType::class)
                                    ->required()
                                    ->default(RequisitionType::BLANKET_ORDER)
                                    ->disabled(fn ($record): bool => $record && $record?->state != RequisitionState::DRAFT)
                                    ->live(),
                                Select::make('currency_id')
                                    ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.form.sections.general.fields.currency'))
                                    ->relationship(
                                        name: 'currency',
                                        titleAttribute: 'name',
                                        modifyQueryUsing: fn (Builder $query) => $query->active(),
                                    )
                                    ->required()
                                    ->searchable()
                                    ->default(current_company()?->currency_id)
                                    ->preload(),
                            ]),

                        Group::make()
                            ->schema([
                                Group::make()
                                    ->schema([
                                        DatePicker::make('starts_at')
                                            ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.form.sections.general.fields.valid-from'))
                                            ->native(false)
                                            ->suffixIcon('heroicon-o-calendar')
                                            ->minDate(now()->toDateString())
                                            ->live()
                                            ->afterStateUpdated(fn (Set $set) => $set('ends_at', null))
                                            ->rules([
                                                'date',
                                                'after_or_equal:today',
                                            ]),
                                        DatePicker::make('ends_at')
                                            ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.form.sections.general.fields.valid-to'))
                                            ->native(false)
                                            ->suffixIcon('heroicon-o-calendar')
                                            ->native(false)
                                            ->minDate(fn (Get $get) => $get('starts_at') ?: now()->toDateString())
                                            ->live()
                                            ->rules([
                                                'date',
                                                'after_or_equal:date_from',
                                            ]),
                                    ])
                                    ->columns(2)
                                    ->hidden(function (Get $get): bool {
                                        return $get('type') != RequisitionType::BLANKET_ORDER;
                                    }),
                                TextInput::make('reference')
                                    ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.form.sections.general.fields.reference'))
                                    ->maxLength(255)
                                    ->placeholder(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.form.sections.general.fields.reference-placeholder')),
                                Select::make('company_id')
                                    ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.form.sections.general.fields.company'))
                                    ->relationship('company', 'name', modifyQueryUsing: fn (Builder $query) => $query->withTrashed())
                                    ->getOptionLabelFromRecordUsing(function ($record): string {
                                        return $record->name.($record->trashed() ? ' (Deleted)' : '');
                                    })
                                    ->disableOptionWhen(function ($label) {
                                        return str_contains($label, ' (Deleted)');
                                    })
                                    ->searchable()
                                    ->required()
                                    ->preload()
                                    ->default(current_company_id())
                                    ->live()
                                    ->afterStateHydrated(static::handleCompanyChange(...))
                                    ->afterStateUpdated(static::handleCompanyChange(...))
                                    ->disabled(fn ($record): bool => $record && $record?->state != RequisitionState::DRAFT),
                            ]),
                    ])
                    ->columns(2),

                Tabs::make()
                    ->schema([
                        Tab::make(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.form.tabs.products.title'))
                            ->schema([
                                static::getProductsRepeater(),
                            ]),

                        Tab::make(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.form.tabs.additional.title'))
                            ->visible(! empty($customFormFields))
                            ->schema($customFormFields),

                        Tab::make(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.form.tabs.terms.title'))
                            ->schema([
                                RichEditor::make('description')
                                    ->hiddenLabel(),
                            ]),
                    ]),
            ])
            ->columns(1);
    }

    public static function getProductsRepeater(): Repeater
    {
        $columns = 4;

        if (PurchaseAgreementResource::getProductSettings()->enable_uom) {
            $columns++;
        }

        return Repeater::make('lines')
            ->hiddenLabel()
            ->relationship()
            ->compact()
            ->table(fn (Get $get) => [
                TableColumn::make('product_id')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.form.tabs.products.columns.product'))
                    ->width(300)
                    ->resizable()
                    ->markAsRequired(),
                TableColumn::make('qty')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.form.tabs.products.columns.quantity'))
                    ->resizable()
                    ->markAsRequired(),
                TableColumn::make('ordered_qty')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.form.tabs.products.columns.ordered'))
                    ->visible($get('type') === RequisitionType::BLANKET_ORDER)

                    ->width(250),
                TableColumn::make('uom_id')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.form.tabs.products.columns.uom'))
                    ->resizable()
                    ->visible(PurchaseAgreementResource::getProductSettings()->enable_uom)
                    ->markAsRequired(),
                TableColumn::make('price_unit')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.form.tabs.products.columns.unit-price'))
                    ->resizable()
                    ->markAsRequired(),
            ])
            ->schema([
                Select::make('product_id')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.form.tabs.products.fields.product'))
                    ->relationship(
                        'product',
                        'name',
                        fn (Builder $query, Get $get) => $query
                            ->where('type', ProductType::GOODS)
                            ->withTrashed()
                            ->whereNull('is_configurable')
                            ->where(owned_by_company($get('../../company_id'))),
                    )
                    ->required()
                    ->searchable()
                    ->preload()
                    ->distinct()
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
                    ->live()
                    ->afterStateUpdated(function (Set $set, Get $get) {
                        if ($product = Product::find($get('product_id'))) {
                            $set('uom_id', $product->uom_id);
                        }
                    })
                    ->disabled(fn ($record): bool => in_array($record?->requisition?->state, [RequisitionState::CLOSED, RequisitionState::CANCELED])),
                TextInput::make('qty')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.form.tabs.products.fields.quantity'))
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(99999999999)
                    ->default(0)
                    ->required()
                    ->disabled(fn ($record): bool => in_array($record?->requisition?->state, [RequisitionState::CLOSED, RequisitionState::CANCELED])),
                TextInput::make('ordered_qty')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.form.tabs.products.fields.ordered'))
                    ->numeric()
                    ->default(0)
                    ->dehydrated(false)
                    ->disabled(),
                Select::make('uom_id')
                    ->label(__('inventories::filament/clusters/operations/resources/operation.form.tabs.operations.fields.unit'))
                    ->relationship(
                        'uom',
                        'name',
                        fn ($query) => $query->where('category_id', 1),
                    )
                    ->searchable()
                    ->preload()
                    ->required()
                    ->wrapOptionLabels(false)
                    ->visible(PurchaseAgreementResource::getProductSettings()->enable_uom)
                    ->disabled(fn ($record): bool => in_array($record?->requisition?->state, [RequisitionState::CLOSED, RequisitionState::CANCELED])),
                TextInput::make('price_unit')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.form.tabs.products.fields.unit-price'))
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(99999999999)
                    ->default(0)
                    ->required()
                    ->disabled(fn ($record): bool => in_array($record?->requisition?->state, [RequisitionState::CLOSED, RequisitionState::CANCELED])),
            ])
            ->columns($columns);
    }

    public static function handleCompanyChange(Get $get, Set $set): void
    {
        $companyCurrencyId = Company::whereCurrencyId($get('company_id'))->value('currency_id');

        if (filled($companyCurrencyId)) {
            $set('currency_id', $companyCurrencyId);
        }
    }
}
