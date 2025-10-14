<?php

namespace Webkul\Invoice\Filament\Clusters\Vendors\Resources;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Webkul\Account\Enums\TypeTaxUse;
use Webkul\Account\Models\Tax;
use Webkul\Field\Filament\Traits\HasCustomFields;
use Webkul\Invoice\Enums\InvoicePolicy;
use Webkul\Invoice\Filament\Clusters\Vendors;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\ProductResource\Pages\CreateProduct;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\ProductResource\Pages\EditProduct;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\ProductResource\Pages\ListProducts;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\ProductResource\Pages\ManageAttributes;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\ProductResource\Pages\ManageVariants;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\ProductResource\Pages\ViewProduct;
use Webkul\Invoice\Models\Product;
use Webkul\Product\Filament\Resources\ProductResource as BaseProductResource;
use Webkul\Support\Models\UOM;

class ProductResource extends BaseProductResource
{
    use HasCustomFields;

    protected static ?string $model = Product::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shopping-bag';

    protected static bool $shouldRegisterNavigation = true;

    protected static ?int $navigationSort = 5;

    protected static ?string $cluster = Vendors::class;

    public static function getNavigationLabel(): string
    {
        return __('invoices::filament/clusters/vendors/resources/product.navigation.title');
    }

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
                    fn ($query) => $query->where('type_tax_use', TypeTaxUse::SALE->value),
                )
                ->multiple()
                ->live()
                ->searchable()
                ->preload(),

            Placeholder::make('total_tax_inclusion')
                ->hiddenLabel()
                ->content(function (Get $get) {
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
                    $result['total_in$this->record->is_configurable = true;cluded'] = $price + $totalTaxAmount;

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
                    fn ($query) => $query->where('type_tax_use', TypeTaxUse::PURCHASE->value),
                )
                ->multiple()
                ->live()
                ->searchable()
                ->preload(),
        ];

        $priceComponent = array_merge($newPriceComponents, $priceComponent);

        $components[1]->getDefaultChildComponents()[1]->schema($priceComponent);

        $childComponents = $components[0]->getDefaultChildComponents();

        $policyComponent = [
            Section::make()
                ->schema([
                    Select::make('invoice_policy')
                        ->label(__('invoices::filament/clusters/vendors/resources/product.form.sections.invoice-policy.title'))
                        ->options(InvoicePolicy::class)
                        ->live()
                        ->default(InvoicePolicy::ORDER->value),
                    Placeholder::make('invoice_policy_help')
                        ->hiddenLabel()
                        ->content(function (Get $get) {
                            return match ($get('invoice_policy')) {
                                InvoicePolicy::ORDER->value    => __('invoices::filament/clusters/vendors/resources/product.form.sections.invoice-policy.ordered-policy'),
                                InvoicePolicy::DELIVERY->value => __('invoices::filament/clusters/vendors/resources/product.form.sections.invoice-policy.delivered-policy'),
                                default                        => '',
                            };
                        }),
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
