<?php

namespace Webkul\Sale\Filament\Clusters\Products\Resources;

use Webkul\Sale\Filament\Clusters\Products;
use Webkul\Sale\Filament\Clusters\Products\Resources\ProductResource\Pages;
use Webkul\Sale\Models\Product;
use Filament\Forms\Form;
use Filament\Forms;
use Filament\Forms\Get;
use Filament\Infolists\Infolist;
use Filament\Pages\SubNavigationPosition;
use Filament\Tables\Table;
use Filament\Resources\Pages\Page;
use Filament\Support\Enums\ActionSize;
use Webkul\Account\Enums\TypeTaxUse;
use Webkul\Account\Models\Tax;
use Webkul\Field\Filament\Traits\HasCustomFields;
use Webkul\Product\Filament\Resources\ProductResource as BaseProductResource;
use Webkul\Sale\Enums\InvoicePolicy;
use Webkul\Sale\Settings\ProductSettings;
use Webkul\Support\Models\UOM;

class ProductResource extends BaseProductResource
{
    use HasCustomFields;

    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $cluster = Products::class;

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Start;

    public static function getNavigationLabel(): string
    {
        return __('sales::filament/clusters/products/resources/product.navigation.title');
    }

    public static function form(Form $form): Form
    {
        $form = BaseProductResource::form($form);

        $components = $form->getComponents();

        $priceComponent = $components[1]->getChildComponents()[1]->getChildComponents();

        $newPriceComponents = [
            Forms\Components\Select::make('accounts_product_taxes')
                ->relationship(
                    'productTaxes',
                    'name',
                    fn($query) => $query->where('type_tax_use', TypeTaxUse::SALE->value),
                )
                ->multiple()
                ->live()
                ->searchable()
                ->preload(),

            Forms\Components\Placeholder::make('total_tax_inclusion')
                ->hiddenLabel()
                ->content(function (Get $get) {
                    $price = floatval($get('price'));
                    $selectedTaxIds = $get('accounts_product_taxes');

                    if (!$price || empty($selectedTaxIds)) {
                        return '';
                    }

                    $taxes = Tax::whereIn('id', $selectedTaxIds)->get();

                    $result = [
                        'total_excluded' => $price,
                        'total_included' => $price,
                        'taxes' => []
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
                            'tax' => $tax,
                            'base' => $price,
                            'amount' => $taxAmount
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

                    return !empty($parts) ? '(= ' . implode(', ', $parts) . ')' : ' ';
                }),

            Forms\Components\Select::make('accounts_product_supplier_taxes')
                ->relationship(
                    'supplierTaxes',
                    'name',
                    fn($query) => $query->where('type_tax_use', TypeTaxUse::PURCHASE->value),
                )
                ->multiple()
                ->live()
                ->searchable()
                ->preload(),
        ];

        $newPriceComponents[] = Forms\Components\Select::make('uom_po_id')
            ->relationship(
                'uomPO',
                'name',
                fn($query) => $query
            )
            ->label('Purchase Unit')
            ->hidden(function () {
                return ! app(ProductSettings::class)?->unit_of_measurement;
            })
            ->default(UOM::first()->id);

        $priceComponent = array_merge($newPriceComponents, $priceComponent);

        $components[1]->getChildComponents()[1]->schema($priceComponent);

        $childComponents = $components[0]->getChildComponents();

        $policyComponent = [
            Forms\Components\Section::make()
                ->visible(fn(Get $get) => $get('sales_ok'))
                ->schema([
                    Forms\Components\Select::make('invoice_policy')
                        ->label(__('sales::filament/clusters/products/resources/product.form.sections.invoice-policy.title'))
                        ->options(InvoicePolicy::class)
                        ->live()
                        ->default(InvoicePolicy::ORDER->value),
                    Forms\Components\Placeholder::make('invoice_policy_help')
                        ->hiddenLabel()
                        ->content(function (Get $get) {
                            return match ($get('invoice_policy')) {
                                InvoicePolicy::ORDER->value => __('sales::filament/clusters/products/resources/product.form.sections.invoice-policy.ordered-policy'),
                                InvoicePolicy::DELIVERY->value => __('sales::filament/clusters/products/resources/product.form.sections.invoice-policy.delivered-policy'),
                                default => '',
                            };
                        }),
                ])
        ];

        array_splice($childComponents, 1, 0, $policyComponent);

        $components[0]->schema($childComponents);

        $firstGroupChildComponents = $components[0]->getChildComponents();

        $secondChildComponents = $firstGroupChildComponents[0]->getChildComponents();

        $newComponents = [
            Forms\Components\Toggle::make('sales_ok')
                ->live()
                ->default(true)
                ->label(__('sales::filament/clusters/products/resources/product.form.sections.general.fields.sales')),
            Forms\Components\Toggle::make('purchase_ok')
                ->default(true)
                ->label(__('sales::filament/clusters/products/resources/product.form.sections.general.fields.purchase')),
        ];

        array_splice($secondChildComponents, 1, 0, $newComponents);

        $favoriteAction = Forms\Components\Actions::make([
            Forms\Components\Actions\Action::make('is_favorite')
                ->hiddenLabel()
                ->outlined(false)
                ->icon(fn($record) => $record?->is_favorite >= 1 ? 'heroicon-s-star' : 'heroicon-o-star')
                ->color('warning')
                ->iconButton()
                ->size(ActionSize::Large->value)
                ->action(fn($record) => $record?->update(['is_favorite' => ! $record->is_favorite,])),
        ]);

        array_unshift($secondChildComponents, $favoriteAction);

        $firstGroupChildComponents[0]->childComponents($secondChildComponents);

        $form->components([
            ...$components,
            Forms\Components\Hidden::make('uom_id')
                ->default(UOM::first()->id),
            Forms\Components\Hidden::make('sale_line_warn')
                ->default('no-message'),
        ]);


        return $form;
    }

    public static function table(Table $table): Table
    {
        return BaseProductResource::table($table);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return BaseProductResource::infolist($infolist);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewProduct::class,
            Pages\EditProduct::class,
            Pages\ManageAttributes::class,
            Pages\ManageVariants::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'      => Pages\ListProducts::route('/'),
            'create'     => Pages\CreateProduct::route('/create'),
            'view'       => Pages\ViewProduct::route('/{record}'),
            'edit'       => Pages\EditProduct::route('/{record}/edit'),
            'attributes' => Pages\ManageAttributes::route('/{record}/attributes'),
            'variants'   => Pages\ManageVariants::route('/{record}/variants'),
        ];
    }
}
