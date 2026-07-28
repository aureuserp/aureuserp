<?php

namespace Webkul\Account\Filament\Resources\ProductResource\Schemas;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Webkul\Account\Enums\InvoicePolicy;
use Webkul\Account\Enums\TypeTaxUse;
use Webkul\Account\Models\Tax;
use Webkul\Account\Settings\DefaultAccountSettings;

/**
 * Account-owned Product schema fragments, contributed to the shared
 * ProductResource form via the right.pricing.fields, left.general.after and
 * hidden slots.
 */
class AccountProductSchema
{
    /**
     * @return array<int, Select>
     */
    public static function taxFields(): array
    {
        return [
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
    }

    public static function policySection(): Section
    {
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

        return Section::make()
            ->schema([
                Select::make('invoice_policy')
                    ->label(__('invoices::filament/clusters/vendors/resources/product.form.sections.invoice-policy.title'))
                    ->options(InvoicePolicy::class)
                    ->live()
                    ->default(InvoicePolicy::ORDER->value)
                    ->helperText(function (Get $get) {
                        return match ($get('invoice_policy')) {
                            InvoicePolicy::ORDER    => __('invoices::filament/clusters/vendors/resources/product.form.sections.invoice-policy.ordered-policy'),
                            InvoicePolicy::DELIVERY => __('invoices::filament/clusters/vendors/resources/product.form.sections.invoice-policy.delivered-policy'),
                            default                 => '',
                        };
                    }),
                $accountPropertiesFieldset,
            ]);
    }

    /**
     * @return array<int, Hidden>
     */
    public static function hiddenFields(): array
    {
        return [
            Hidden::make('sale_line_warn')
                ->default('no-message'),
        ];
    }
}
