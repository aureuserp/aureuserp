<?php

namespace Webkul\Account\Filament\Resources;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Webkul\Account\Enums\DisplayType;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Enums\TypeTaxUse;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Account\Facades\Tax as TaxFacade;
use Webkul\Account\Filament\Resources\BillResource\Pages\CreateBill;
use Webkul\Account\Filament\Resources\BillResource\Pages\EditBill;
use Webkul\Account\Filament\Resources\BillResource\Pages\ListBills;
use Webkul\Account\Filament\Resources\BillResource\Pages\ViewBill;
use Webkul\Account\Filament\Resources\BillResource\Schemas\BillForm;
use Webkul\Account\Filament\Resources\BillResource\Schemas\BillInfolist;
use Webkul\Account\Filament\Resources\BillResource\Tables\BillsTable;
use Webkul\Account\Livewire\InvoiceSummary;
use Webkul\Account\Models\Bill;
use Webkul\Account\Models\CashRounding;
use Webkul\Account\Models\MoveLine;
use Webkul\Account\Models\Product;
use Webkul\Account\Models\Tax;
use Webkul\Field\Filament\Traits\HasCustomFields;
use Webkul\Product\Settings\ProductSettings;
use Webkul\Support\Filament\Forms\Components\Repeater;
use Webkul\Support\Filament\Forms\Components\Repeater\TableColumn;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;
use Webkul\Support\Models\UOM;

class BillResource extends Resource
{
    use HasCustomFields;

    protected static ?string $model = Bill::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static bool $shouldRegisterNavigation = false;

    protected static bool $isGloballySearchable = false;

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('accounts::filament/resources/bill.global-search.vendor')   => $record->partner?->name ?? '—',
            __('accounts::filament/resources/bill.global-search.date')     => $record?->invoice_date ?? '—',
            __('accounts::filament/resources/bill.global-search.due-date') => $record?->invoice_date_due ?? '—',
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return BillForm::configure($schema, static::class, static::getCustomFormFields());
    }

    public static function table(Table $table): Table
    {
        return BillsTable::configure($table, static::class, static::getCustomTableColumns(), static::getCustomTableFilters());
    }

    public static function infolist(Schema $schema): Schema
    {
        return BillInfolist::configure($schema, static::class, static::getCustomInfolistEntries());
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListBills::route('/'),
            'create' => CreateBill::route('/create'),
            'edit'   => EditBill::route('/{record}/edit'),
            'view'   => ViewBill::route('/{record}'),
        ];
    }

    public static function getProductRepeater(): Repeater
    {
        return Repeater::make('products')
            ->relationship('invoiceLines')
            ->hiddenLabel()
            ->live()
            ->reactive()
            ->label(__('accounts::filament/resources/bill.form.tabs.invoice-lines.repeater.products.title'))
            ->addActionLabel(__('accounts::filament/resources/bill.form.tabs.invoice-lines.repeater.products.add-product'))
            ->collapsible()
            ->compact()
            ->defaultItems(0)
            ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
            ->deleteAction(fn (Action $action) => $action->requiresConfirmation())
            ->deletable(fn ($record): bool => ! in_array($record?->state, [MoveState::POSTED, MoveState::CANCEL]))
            ->addable(fn ($record): bool => ! in_array($record?->state, [MoveState::POSTED, MoveState::CANCEL]))
            ->table([
                TableColumn::make('product_id')
                    ->label(__('accounts::filament/resources/bill.form.tabs.invoice-lines.repeater.products.columns.product'))
                    ->width(300)
                    ->resizable()
                    ->markAsRequired()
                    ->toggleable(),
                TableColumn::make('quantity')
                    ->label(__('accounts::filament/resources/bill.form.tabs.invoice-lines.repeater.products.columns.quantity'))
                    ->resizable()
                    ->markAsRequired()
                    ->toggleable(),
                TableColumn::make('uom_id')
                    ->label(__('accounts::filament/resources/bill.form.tabs.invoice-lines.repeater.products.columns.unit'))
                    ->resizable()
                    ->markAsRequired()
                    ->visible(fn () => settings(ProductSettings::class)->enable_uom)
                    ->toggleable(),
                TableColumn::make('price_unit')
                    ->label(__('accounts::filament/resources/bill.form.tabs.invoice-lines.repeater.products.columns.unit-price'))
                    ->resizable()
                    ->markAsRequired(),
                TableColumn::make('discount')
                    ->label(__('accounts::filament/resources/bill.form.tabs.invoice-lines.repeater.products.columns.discount-percentage'))
                    ->resizable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TableColumn::make('taxes')
                    ->label(__('accounts::filament/resources/bill.form.tabs.invoice-lines.repeater.products.columns.taxes'))
                    ->resizable()
                    ->toggleable(),
                TableColumn::make('price_subtotal')
                    ->label(__('accounts::filament/resources/bill.form.tabs.invoice-lines.repeater.products.columns.sub-total'))
                    ->resizable()
                    ->toggleable(),
            ])
            ->schema([
                Select::make('product_id')
                    ->label(__('accounts::filament/resources/bill.form.tabs.invoice-lines.repeater.products.fields.product'))
                    ->relationship(
                        name: 'product',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn (Builder $query, Get $get) => $query
                            ->withTrashed()
                            ->whereNull('is_configurable')
                            ->where(owned_by_company($get('../../company_id'))),
                    )
                    ->searchable()
                    ->preload()
                    ->live()
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
                    ->dehydrated()
                    ->disabled(fn ($record) => in_array($record?->parent_state, [MoveState::POSTED, MoveState::CANCEL]))
                    ->afterStateUpdated(fn (Set $set, Get $get) => static::afterProductUpdated($set, $get))
                    ->required(),
                TextInput::make('quantity')
                    ->label(__('accounts::filament/resources/bill.form.tabs.invoice-lines.repeater.products.fields.quantity'))
                    ->required()
                    ->default(1)
                    ->numeric()
                    ->maxValue(99999999999)
                    ->live(onBlur: true)
                    ->dehydrated()
                    ->disabled(fn ($record) => in_array($record?->parent_state, [MoveState::POSTED, MoveState::CANCEL]))
                    ->afterStateUpdated(fn (Set $set, Get $get) => static::afterProductQtyUpdated($set, $get)),
                Select::make('uom_id')
                    ->label(__('accounts::filament/resources/bill.form.tabs.invoice-lines.repeater.products.fields.unit'))
                    ->relationship(
                        'uom',
                        'name',
                        function (Builder $query, Get $get) {
                            $product = Product::find($get('product_id'));
                            $categoryId = $product?->uom?->category_id;

                            return $query->when($categoryId, fn ($q) => $q->where('category_id', $categoryId))->orderBy('id');
                        },
                    )
                    ->wrapOptionLabels(false)
                    ->required()
                    ->live()
                    ->native(false)
                    ->selectablePlaceholder(false)
                    ->dehydrated()
                    ->disabled(fn ($record) => in_array($record?->parent_state, [MoveState::POSTED, MoveState::CANCEL]))
                    ->afterStateUpdated(fn (Set $set, Get $get) => static::afterUOMUpdated($set, $get))
                    ->visible(fn (ProductSettings $settings) => $settings->enable_uom),
                TextInput::make('price_unit')
                    ->label(__('accounts::filament/resources/bill.form.tabs.invoice-lines.repeater.products.fields.unit-price'))
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->maxValue(99999999999)
                    ->required()
                    ->live(onBlur: true)
                    ->dehydrated()
                    ->disabled(fn ($record) => in_array($record?->parent_state, [MoveState::POSTED, MoveState::CANCEL]))
                    ->afterStateUpdated(fn (Set $set, Get $get) => self::calculateLineTotals($set, $get)),
                TextInput::make('discount')
                    ->label(__('accounts::filament/resources/bill.form.tabs.invoice-lines.repeater.products.fields.discount-percentage'))
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->maxValue(99999999999)
                    ->live(onBlur: true)
                    ->dehydrated()
                    ->disabled(fn ($record) => in_array($record?->parent_state, [MoveState::POSTED, MoveState::CANCEL]))
                    ->afterStateUpdated(fn (Set $set, Get $get) => self::calculateLineTotals($set, $get)),
                Select::make('taxes')
                    ->label(__('accounts::filament/resources/bill.form.tabs.invoice-lines.repeater.products.fields.taxes'))
                    ->relationship(
                        'taxes',
                        'name',
                        modifyQueryUsing: fn (Builder $query, Get $get) => $query
                            ->where('type_tax_use', TypeTaxUse::PURCHASE)
                            ->where(owned_by_company($get('../../company_id'))),
                    )
                    ->wrapOptionLabels(false)
                    ->searchable()
                    ->multiple()
                    ->preload()
                    ->dehydrated()
                    ->disabled(fn ($record) => in_array($record?->parent_state, [MoveState::POSTED, MoveState::CANCEL]))
                    ->afterStateHydrated(fn (Get $get, Set $set) => self::calculateLineTotals($set, $get))
                    ->afterStateUpdated(fn (Get $get, Set $set) => self::calculateLineTotals($set, $get))
                    ->live(),
                TextInput::make('price_subtotal')
                    ->label(__('accounts::filament/resources/bill.form.tabs.invoice-lines.repeater.products.fields.sub-total'))
                    ->default(0)
                    ->dehydrated()
                    ->disabled(fn ($record) => in_array($record?->parent_state, [MoveState::POSTED, MoveState::CANCEL])),
                Hidden::make('product_uom_qty')
                    ->default(0),
                Hidden::make('price_tax')
                    ->default(0),
                Hidden::make('price_total')
                    ->default(0),
            ])
            ->mutateRelationshipDataBeforeCreateUsing(fn (array $data, $record) => static::mutateProductRelationship($data, $record))
            ->mutateRelationshipDataBeforeSaveUsing(fn (array $data, $record) => static::mutateProductRelationship($data, $record));
    }

    public static function getSummaryComponent()
    {
        return InvoiceSummary::class;
    }

    public static function mutateProductRelationship(array $data, $record): array
    {
        $data['currency_id'] = $record->currency_id;

        return $data;
    }

    private static function afterProductUpdated(Set $set, Get $get): void
    {
        if (! $get('product_id')) {
            return;
        }

        $product = Product::find($get('product_id'));

        $set('uom_id', $product->uom_id);

        $priceUnit = static::calculateUnitPrice($product->uom_id, $product);

        if ($get('../../currency_id')) {
            $currency = Currency::find($get('../../currency_id'));

            $company = Company::find($get('../../company_id')) ?? current_company();

            $priceUnit = $company->currency->convert(
                $priceUnit,
                $currency,
                $company
            );
        }

        $set('price_unit', round($priceUnit, 2));

        $set('taxes', Tax::forProduct($product, TypeTaxUse::PURCHASE, $get('../../company_id')));

        $uomQuantity = static::calculateUnitQuantity($get('uom_id'), $get('quantity'));

        $set('product_uom_qty', round($uomQuantity, 2));

        self::calculateLineTotals($set, $get);
    }

    private static function afterProductQtyUpdated(Set $set, Get $get): void
    {
        if (! $get('product_id')) {
            return;
        }

        $uomQuantity = static::calculateUnitQuantity($get('uom_id'), $get('quantity'));

        $set('product_uom_qty', round($uomQuantity, 2));

        self::calculateLineTotals($set, $get);
    }

    private static function afterUOMUpdated(Set $set, Get $get): void
    {
        if (! $get('product_id')) {
            return;
        }

        $uomQuantity = static::calculateUnitQuantity($get('uom_id'), $get('quantity'));

        $set('product_uom_qty', round($uomQuantity, 2));

        $product = Product::find($get('product_id'));

        $priceUnit = static::calculateUnitPrice($get('uom_id'), $product);

        $set('price_unit', round($priceUnit, 2));

        self::calculateLineTotals($set, $get);
    }

    private static function calculateUnitQuantity($uomId, $quantity)
    {
        if (! $uomId || ! filled($quantity)) {
            return (float) ($quantity ?? 0);
        }

        $fromUom = UOM::find($uomId);

        if (! $fromUom) {
            return (float) ($quantity ?? 0);
        }

        $referenceUom = UOM::where('category_id', $fromUom->category_id)->orderBy('factor')->first();

        if (! $referenceUom) {
            return (float) ($quantity ?? 0);
        }

        return $fromUom->computeQuantity((float) ($quantity ?? 0), $referenceUom, false);
    }

    private static function calculateUnitPrice($uomId, $product)
    {
        $price = $product->price ?? $product->cost;

        if (! $uomId || ! $product->uom) {
            return $price;
        }

        $uomQty = UOM::find($uomId)->computeQuantity(1, $product->uom, false);

        return (float) ($price * $uomQty);
    }

    private static function calculateLineTotals(Set $set, Get $get): void
    {
        if (! $get('product_id')) {
            $set('price_unit', 0);
            $set('discount', 0);
            $set('price_tax', 0);
            $set('price_subtotal', 0);
            $set('price_total', 0);

            return;
        }

        $currencyId = $get('../../currency_id');
        $companyId = $get('../../company_id');
        $productId = $get('product_id');

        if (! $currencyId || ! $companyId || ! $productId) {
            return;
        }

        $currency = Currency::find($currencyId);
        $company = Company::find($companyId);
        $product = Product::find($productId);

        if (! $currency || ! $company || ! $product) {
            return;
        }

        $mockLine = new MoveLine([
            'quantity'     => $get('quantity') ?? 1,
            'price_unit'   => $get('price_unit') ?? 0,
            'discount'     => $get('discount') ?? 0,
            'display_type' => DisplayType::PRODUCT,
        ]);

        $mockMove = new Bill([
            'move_type'   => $get('../../move_type'),
            'currency_id' => $currencyId,
            'company_id'  => $companyId,
        ]);

        $taxIds = $get('taxes') ?? [];
        $mockLine->setRelation('taxes', Tax::whereIn('id', $taxIds)->get());
        $mockLine->setRelation('currency', $currency);
        $mockLine->setRelation('company', $company);
        $mockLine->setRelation('product', $product);
        $mockLine->setRelation('move', $mockMove);

        $mockMove->setRelation('currency', $currency);
        $mockMove->setRelation('company', $company);

        $baseLine = AccountFacade::productBaseLine($mockLine);

        $baseLine = TaxFacade::withTaxDetails($baseLine, $company);

        $subtotal = $baseLine['tax_details']['raw_total_excluded_currency'];
        $total = $baseLine['tax_details']['raw_total_included_currency'];
        $tax = $total - $subtotal;

        $set('price_subtotal', round($subtotal, 4));
        $set('price_tax', round($tax, 4));
        $set('price_total', round($total, 4));
    }

    public static function calculateMoveTotals(Get $get, $livewire): array
    {
        $defaultTotals = [
            'subtotal'   => 0,
            'totalTax'   => 0,
            'grandTotal' => 0,
            'rounding'   => 0,
        ];

        $currencyId = $get('currency_id');
        $companyId = $get('company_id');
        $products = $get('products') ?? [];

        if (! $currencyId || ! $companyId || empty($products)) {
            $livewire->dispatch('itemUpdated', $defaultTotals);

            return $defaultTotals;
        }

        $currency = Currency::find($currencyId);
        $company = Company::find($companyId);

        if (! $currency || ! $company) {
            $livewire->dispatch('itemUpdated', $defaultTotals);

            return $defaultTotals;
        }

        $cashRoundingId = $get('invoice_cash_rounding_id');

        $mockMove = new Bill([
            'move_type'                => $get('move_type'),
            'currency_id'              => $currency->id,
            'company_id'               => $company->id,
            'invoice_cash_rounding_id' => $cashRoundingId,
        ]);

        $mockMove->setRelation('currency', $currency);
        $mockMove->setRelation('company', $company);

        if ($cashRoundingId) {
            $cashRounding = CashRounding::find($cashRoundingId);

            if ($cashRounding) {
                $mockMove->setRelation('invoiceCashRounding', $cashRounding);
            }
        }

        $mockLines = collect($products)
            ->filter(fn ($productData) => ! empty($productData['product_id']))
            ->map(function ($productData) use ($currency, $company, $mockMove) {
                $product = Product::find($productData['product_id']);

                if (! $product) {
                    return null;
                }

                $mockLine = new MoveLine([
                    'quantity'     => $productData['quantity'] ?? 1,
                    'price_unit'   => $productData['price_unit'] ?? 0,
                    'discount'     => $productData['discount'] ?? 0,
                    'display_type' => DisplayType::PRODUCT,
                ]);

                $mockLine->setRelation('taxes', Tax::whereIn('id', $productData['taxes'] ?? [])->get());
                $mockLine->setRelation('currency', $currency);
                $mockLine->setRelation('company', $company);
                $mockLine->setRelation('product', $product);
                $mockLine->setRelation('move', $mockMove);

                return $mockLine;
            })
            ->filter();

        if ($mockLines->isEmpty()) {
            $livewire->dispatch('itemUpdated', $defaultTotals);

            return $defaultTotals;
        }

        $mockMove->setRelation('lines', $mockLines);

        [$baseLines] = AccountFacade::roundedBaseAndTaxLines($mockMove, false);

        $subtotal = 0;
        $grandTotal = 0;
        $rounding = 0;

        foreach ($baseLines as $baseLine) {
            $specialType = $baseLine['special_type'] ?? null;

            if ($specialType === 'cash_rounding') {
                $rounding = $baseLine['tax_details']['raw_total_excluded_currency'];
            } else {
                $subtotal += $baseLine['tax_details']['raw_total_excluded_currency'] ?? 0;
                $grandTotal += $baseLine['tax_details']['raw_total_included_currency'] ?? 0;
            }
        }

        if ($rounding == 0 && $cashRoundingId) {
            $cashRounding = CashRounding::find($cashRoundingId);

            if ($cashRounding) {
                $rounding = $cashRounding->computeDifference($currency, $grandTotal);
            }
        }

        $defaultTotals = [
            'subtotal'   => round($subtotal, 2),
            'totalTax'   => round($grandTotal - $subtotal, 2),
            'grandTotal' => round($grandTotal, 2),
            'rounding'   => round($rounding, 2),
        ];

        $livewire->dispatch('itemUpdated', $defaultTotals);

        return $defaultTotals;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->when(Str::contains(static::class, 'BillResource'), function (Builder $query) {
                $query->where('move_type', MoveType::IN_INVOICE);
            })
            ->orderByDesc('id');
    }

    public static function disableTypeField($component): void
    {
        if (method_exists($component, 'getChildComponents')) {
            foreach ($component->getChildComponents() as $child) {
                static::disableTypeField($child);
            }
        }

        if (method_exists($component, 'getName') && $component->getName() === 'type') {
            $component->disabled()->dehydrated();
        }
    }
}
