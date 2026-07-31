<?php

namespace Webkul\Account\Filament\Resources\ProductResource\Schemas;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Webkul\Account\Enums\AccountType;
use Webkul\Account\Enums\InvoicePolicy;
use Webkul\Account\Enums\TypeTaxUse;
use Webkul\Account\Models\Account;
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
                    ->key(fn (Get $get) => 'property_account_income_id.'.($get('company_id') ?? 'all'))
                    ->label(__('accounts::filament/resources/category.form.fieldsets.account-properties.fields.income-account'))
                    ->hintIcon(
                        'heroicon-m-question-mark-circle',
                        tooltip: __('accounts::filament/resources/category.form.fieldsets.account-properties.fields.income-account-hint-tooltip')
                    )
                    ->options(fn (Get $get) => static::accountOptions($get('company_id')))
                    ->getOptionLabelUsing(fn ($value, Get $get) => static::accountOptions($get('company_id'))[$value] ?? null)
                    ->preload()
                    ->searchable()
                    ->default(fn (Get $get, DefaultAccountSettings $settings) => Account::resolveForCompany(
                        $settings->income_account_id,
                        $get('company_id'),
                    ))
                    ->afterStateHydrated(function (Select $component, $state, Get $get, DefaultAccountSettings $settings): void {
                        if (filled($state) && array_key_exists((int) $state, static::accountOptions($get('company_id')))) {
                            return;
                        }

                        $component->state(Account::resolveForCompany($settings->income_account_id, $get('company_id')));
                    }),

                Select::make('property_account_expense_id')
                    ->key(fn (Get $get) => 'property_account_expense_id.'.($get('company_id') ?? 'all'))
                    ->label(__('accounts::filament/resources/category.form.fieldsets.account-properties.fields.expense-account'))
                    ->hintIcon(
                        'heroicon-m-question-mark-circle',
                        tooltip: __('accounts::filament/resources/category.form.fieldsets.account-properties.fields.expense-account-hint-tooltip')
                    )
                    ->options(fn (Get $get) => static::accountOptions($get('company_id')))
                    ->getOptionLabelUsing(fn ($value, Get $get) => static::accountOptions($get('company_id'))[$value] ?? null)
                    ->preload()
                    ->searchable()
                    ->default(fn (Get $get, DefaultAccountSettings $settings) => Account::resolveForCompany(
                        $settings->expense_account_id,
                        $get('company_id'),
                    ))
                    ->afterStateHydrated(function (Select $component, $state, Get $get, DefaultAccountSettings $settings): void {
                        if (filled($state) && array_key_exists((int) $state, static::accountOptions($get('company_id')))) {
                            return;
                        }

                        $component->state(Account::resolveForCompany($settings->expense_account_id, $get('company_id')));
                    }),
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

    protected static function accountOptions($companyId): array
    {

        return Account::query()
            ->where('deprecated', false)
            ->whereNotIn('account_type', [
                AccountType::ASSET_RECEIVABLE,
                AccountType::LIABILITY_PAYABLE,
                AccountType::ASSET_CASH,
                AccountType::LIABILITY_CREDIT_CARD,
                AccountType::OFF_BALANCE,
            ])
            ->where(owned_by_company($companyId))
            ->orderBy('name')
            ->pluck('name', 'id')
            ->all();
    }
}
