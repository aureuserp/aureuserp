<?php

namespace Webkul\Account\Filament\Resources;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Webkul\Account\Enums\InvoicePolicy;
use Webkul\Account\Enums\TypeTaxUse;
use Webkul\Account\Filament\Resources\ProductResource\Pages\CreateProduct;
use Webkul\Account\Filament\Resources\ProductResource\Pages\EditProduct;
use Webkul\Account\Filament\Resources\ProductResource\Pages\ListProducts;
use Webkul\Account\Filament\Resources\ProductResource\Pages\ManageAttributes;
use Webkul\Account\Filament\Resources\ProductResource\Pages\ManageVariants;
use Webkul\Account\Filament\Resources\ProductResource\Pages\ViewProduct;
use Webkul\Account\Models\Product;
use Webkul\Account\Models\Tax;
use Webkul\Account\Settings\DefaultAccountSettings;
use Webkul\Product\Filament\Resources\ProductResource as BaseProductResource;
use Webkul\Support\Models\UOM;

class ProductResource extends BaseProductResource
{
    protected static ?string $model = Product::class;

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        $schema = BaseProductResource::form($schema);

        $components = $schema->getComponents();

        $priceComponent = $components[1]->getDefaultChildComponents()[1]->getDefaultChildComponents();

        $newPriceComponents = [
            Select::make('accounts_product_taxes')
                ->relationship(
                    'productTaxes',
                    'name',
                    modifyQueryUsing: fn ($query) => $query->where('type_tax_use', TypeTaxUse::SALE),
                )
                ->multiple()
                ->live()
                ->searchable()
                ->preload()
                ->helperText(function (Get $get) {
                    $price = floatval($get('price'));

                    $selectedTaxIds = $get('accounts_product_taxes');

                    if (! $price || empty($selectedTaxIds)) {
                        return '';
                    }

                    $taxes = Tax::whereIn('id', $selectedTaxIds)->get();

                    $result = [
                        'total_excluded' => $price,
                        'total_included' => $price,
                        'taxes'          => [],
                    ];

                    $totalTaxAmount = 0;

                    $basePrice = $price;

                    foreach ($taxes as $tax) {
                        $taxAmount = $basePrice * ($tax->amount / 100);
                        $totalTaxAmount += $taxAmount;

                        if ($tax->include_base_amount) {
                            $basePrice += $taxAmount;
                        }

                        $result['taxes'][] = [
                            'tax'    => $tax,
                            'base'   => $price,
                            'amount' => $taxAmount,
                        ];
                    }

                    $result['total_excluded'] = $price;
                    $result['total_included'] = $price + $totalTaxAmount;

                    $parts = [];

                    if ($result['total_included'] != $price) {
                        $parts[] = sprintf(
                            '%s Incl. Taxes',
                            number_format($result['total_included'], 2)
                        );
                    }

                    if ($result['total_excluded'] != $price) {
                        $parts[] = sprintf(
                            '%s Excl. Taxes',
                            number_format($result['total_excluded'], 2)
                        );
                    }

                    return ! empty($parts) ? '(= '.implode(', ', $parts).')' : ' ';
                }),

            Select::make('accounts_product_supplier_taxes')
                ->relationship(
                    'supplierTaxes',
                    'name',
                    modifyQueryUsing: fn ($query) => $query->where('type_tax_use', TypeTaxUse::PURCHASE),
                )
                ->multiple()
                ->live()
                ->searchable()
                ->preload(),
        ];

        $priceComponent = array_merge($newPriceComponents, $priceComponent);

        $components[1]->getDefaultChildComponents()[1]->schema($priceComponent);

        $childComponents = $components[0]->getDefaultChildComponents();

        $accountPropertiesFieldset = Fieldset::make()
            ->label(__('accounts::filament/resources/category.form.fieldsets.account-properties.label'))
            ->schema([
                Select::make('property_account_income_id')
                    ->label(__('accounts::filament/resources/category.form.fieldsets.account-properties.fields.income-account'))
                    ->hintIcon(
                        'heroicon-m-question-mark-circle',
                        tooltip: __('accounts::filament/resources/category.form.fieldsets.account-properties.fields.income-account-hint-tooltip')
                    )
                    ->relationship('propertyAccountIncome', 'name')
                    ->preload()
                    ->searchable()
                    ->default(fn (DefaultAccountSettings $settings) => $settings->income_account_id),

                Select::make('property_account_expense_id')
                    ->label(__('accounts::filament/resources/category.form.fieldsets.account-properties.fields.expense-account'))
                    ->hintIcon(
                        'heroicon-m-question-mark-circle',
                        tooltip: __('accounts::filament/resources/category.form.fieldsets.account-properties.fields.expense-account-hint-tooltip')
                    )
                    ->relationship('propertyAccountExpense', 'name')
                    ->preload()
                    ->searchable()
                    ->default(fn (DefaultAccountSettings $settings) => $settings->expense_account_id),
            ]);

        $policyComponent = [
            Section::make()
                ->schema([
                    Select::make('invoice_policy')
                        ->label(__('invoices::filament/clusters/vendors/resources/product.form.sections.invoice-policy.title'))
                        ->options(InvoicePolicy::class)
                        ->live()
                        ->default(InvoicePolicy::ORDER->value)
                        ->helperText(function (Get $get) {
                            return match ($get('invoice_policy')) {
                                InvoicePolicy::ORDER           => __('invoices::filament/clusters/vendors/resources/product.form.sections.invoice-policy.ordered-policy'),
                                InvoicePolicy::DELIVERY        => __('invoices::filament/clusters/vendors/resources/product.form.sections.invoice-policy.delivered-policy'),
                                default                        => '',
                            };
                        }),
                    $accountPropertiesFieldset,
                ]),
        ];

        array_splice($childComponents, 1, 0, $policyComponent);

        $components[0]->schema($childComponents);

        $schema->components([
            ...$components,
            Hidden::make('uom_id')
                ->default(UOM::first()->id),
            Hidden::make('uom_po_id')
                ->default(UOM::first()->id),
            Hidden::make('sale_line_warn')
                ->default('no-message'),
        ]);

        return $schema;
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewProduct::class,
            EditProduct::class,
            ManageAttributes::class,
            ManageVariants::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'      => ListProducts::route('/'),
            'create'     => CreateProduct::route('/create'),
            'view'       => ViewProduct::route('/{record}'),
            'edit'       => EditProduct::route('/{record}/edit'),
            'attributes' => ManageAttributes::route('/{record}/attributes'),
            'variants'   => ManageVariants::route('/{record}/variants'),
        ];
    }
}
