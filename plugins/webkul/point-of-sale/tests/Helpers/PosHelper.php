<?php

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Webkul\Account\Enums\AccountType;
use Webkul\Account\Enums\DocumentType;
use Webkul\Account\Enums\JournalType;
use Webkul\Account\Enums\RepartitionType;
use Webkul\Account\Enums\RoundingMethod;
use Webkul\Account\Enums\RoundingStrategy;
use Webkul\Account\Models\Account;
use Webkul\Account\Models\CashRounding;
use Webkul\Account\Models\Journal;
use Webkul\Account\Models\Tax;
use Webkul\Account\Models\TaxPartition;
use Webkul\Inventory\Models\Location;
use Webkul\Inventory\Models\ProductQuantity;
use Webkul\Inventory\Models\Warehouse as InventoryWarehouse;
use Webkul\PointOfSale\Enums\PaymentMethodType;
use Webkul\PointOfSale\Enums\StockUpdateMode;
use Webkul\PointOfSale\Facades\PointOfSale;
use Webkul\PointOfSale\Models\Bill;
use Webkul\PointOfSale\Models\Category;
use Webkul\PointOfSale\Models\Config;
use Webkul\PointOfSale\Models\Floor;
use Webkul\PointOfSale\Models\PaymentMethod;
use Webkul\PointOfSale\Models\Session;
use Webkul\PointOfSale\Models\Table;
use Webkul\PointOfSale\Models\Warehouse;
use Webkul\PointOfSale\Settings\InventorySettings;
use Webkul\Product\Enums\PriceRuleApplyTo;
use Webkul\Product\Enums\PriceRuleBase;
use Webkul\Product\Enums\PriceRuleType;
use Webkul\Product\Models\PriceList;
use Webkul\Product\Models\PriceRuleItem;
use Webkul\Product\Models\Product;
use Webkul\Support\Models\Company;

class PosHelper
{
    public static function ensureRoutesRegistered(): void
    {
        if (Route::has('admin.api.v1.point-of-sale.orders.sync')) {
            return;
        }

        require base_path('plugins/webkul/point-of-sale/routes/api.php');

        $routes = app('router')->getRoutes();

        $routes->refreshNameLookups();

        $routes->refreshActionLookups();
    }

    public static function company(): Company
    {
        return Company::query()->firstOrFail();
    }

    public static function warehouse(InventoryWarehouse $warehouse): Warehouse
    {
        return Warehouse::findOrFail($warehouse->id);
    }

    public static function saleJournal(array $overrides = []): Journal
    {
        return Journal::query()
            ->where('type', JournalType::SALE)
            ->where('company_id', static::company()->id)
            ->firstOr(fn () => Journal::factory()->create(array_merge([
                'type'       => JournalType::SALE,
                'company_id' => static::company()->id,
            ], $overrides)));
    }

    public static function cashJournal(array $overrides = []): Journal
    {
        return Journal::factory()->create(array_merge([
            'type'       => JournalType::CASH,
            'company_id' => static::company()->id,
        ], $overrides));
    }

    public static function stockUpdateMode(StockUpdateMode $mode): void
    {
        $settings = settings(InventorySettings::class);

        $settings->stock_update_mode = $mode;

        $settings->save();
    }

    public static function receivableAccount(): Account
    {
        return Account::query()
            ->where('account_type', AccountType::ASSET_RECEIVABLE)
            ->firstOrFail();
    }

    public static function config(InventoryWarehouse $warehouse, array $overrides = []): Config
    {
        $posWarehouse = static::warehouse($warehouse);

        return Config::create(array_merge([
            'receivable_account_id'    => static::receivableAccount()->id,
            'name'                     => 'Shop '.$warehouse->code,
            'code'                     => $warehouse->code.'POS',
            'warehouse_id'             => $posWarehouse->id,
            'operation_type_id'        => $posWarehouse->pos_type_id,
            'return_operation_type_id' => $posWarehouse->pos_return_type_id,
            'invoice_journal_id'       => static::saleJournal()->id,
            'company_id'               => static::company()->id,
        ], $overrides));
    }

    public static function cashMethod(array $overrides = []): PaymentMethod
    {
        $attributes = array_merge([
            'name'       => 'Cash',
            'journal_id' => static::cashJournal()->id,
            'company_id' => static::company()->id,
        ], $overrides);

        return PaymentMethod::firstOrCreate(
            Arr::only($attributes, ['name', 'company_id']),
            $attributes,
        );
    }

    public static function bankMethod(array $overrides = []): PaymentMethod
    {
        return PaymentMethod::create(array_merge([
            'name'       => 'Card',
            'type'       => PaymentMethodType::BANK,
            'journal_id' => static::bankJournal()->id,
            'company_id' => static::company()->id,
        ], $overrides));
    }

    public static function bankJournal(array $overrides = []): Journal
    {
        return Journal::firstOrCreate(
            [
                'code'       => 'POSBK',
                'company_id' => static::company()->id,
            ],
            array_merge([
                'name'       => 'Point of Sale Bank',
                'type'       => JournalType::BANK,
                'company_id' => static::company()->id,
            ], $overrides),
        );
    }

    public static function cashRounding(array $overrides = []): CashRounding
    {
        return CashRounding::create(array_merge([
            'name'              => 'Nickel',
            'rounding'          => 0.05,
            'strategy'          => RoundingStrategy::ADD_INVOICE_LINE,
            'rounding_method'   => RoundingMethod::HALF_UP,
            'profit_account_id' => static::incomeAccount()->id,
            'loss_account_id'   => static::expenseAccount()->id,
        ], $overrides));
    }

    public static function payLaterMethod(array $overrides = []): PaymentMethod
    {
        return PaymentMethod::create(array_merge([
            'name'       => 'Customer Account',
            'journal_id' => null,
            'company_id' => static::company()->id,
        ], $overrides));
    }

    public static function configWithCashMethod(InventoryWarehouse $warehouse, array $overrides = []): Config
    {
        $config = static::config($warehouse, $overrides);

        if ($config->paymentMethods()->where('is_cash_count', true)->doesntExist()) {
            $config->paymentMethods()->attach(static::cashMethod());
        }

        return $config->refresh();
    }

    public static function bill(float $value): Bill
    {
        return Bill::query()->firstOrCreate(
            ['name' => number_format($value, 2, '.', '')],
            ['value' => $value, 'is_for_all_configs' => true],
        );
    }

    public static function openSession(InventoryWarehouse $warehouse, float $cashBalanceStart = 0.0): Session
    {
        $config = static::configWithCashMethod($warehouse);

        $session = PointOfSale::openSession($config);

        return PointOfSale::confirmSessionOpeningControl($session, $cashBalanceStart);
    }

    public static function orderPayload(Config $config, Session $session, array $lines, array $payments = [], array $overrides = []): array
    {
        return array_merge([
            'uuid'       => (string) Str::uuid(),
            'config_id'  => $config->id,
            'session_id' => $session->id,
            'lines'      => $lines,
            'payments'   => $payments,
        ], $overrides);
    }

    public static function line(int $productId, float $qty, float $priceUnit, array $overrides = []): array
    {
        return array_merge([
            'uuid'       => (string) Str::uuid(),
            'product_id' => $productId,
            'qty'        => $qty,
            'price_unit' => $priceUnit,
        ], $overrides);
    }

    public static function payment(PaymentMethod $paymentMethod, float $amount, array $overrides = []): array
    {
        return array_merge([
            'uuid'              => (string) Str::uuid(),
            'payment_method_id' => $paymentMethod->id,
            'amount'            => $amount,
        ], $overrides);
    }

    public static function incomeAccount(): Account
    {
        return Account::query()
            ->where('account_type', AccountType::INCOME)
            ->firstOrFail();
    }

    public static function expenseAccount(): Account
    {
        return Account::query()
            ->where('account_type', AccountType::EXPENSE)
            ->firstOrFail();
    }

    public static function taxAccount(): Account
    {
        return Account::query()
            ->where('account_type', AccountType::LIABILITY_CURRENT)
            ->firstOr(fn () => static::incomeAccount());
    }

    public static function taxWithAccounts(float $amount = 10.0): Tax
    {
        $tax = Tax::factory()->create([
            'name'       => 'POS Tax '.$amount,
            'amount'     => $amount,
            'company_id' => static::company()->id,
        ]);

        $account = static::taxAccount();

        foreach ([DocumentType::INVOICE, DocumentType::REFUND] as $documentType) {
            TaxPartition::create([
                'tax_id'           => $tax->id,
                'document_type'    => $documentType,
                'repartition_type' => RepartitionType::BASE,
                'factor_percent'   => 100,
                'company_id'       => static::company()->id,
            ]);

            TaxPartition::create([
                'tax_id'           => $tax->id,
                'document_type'    => $documentType,
                'repartition_type' => RepartitionType::TAX,
                'factor_percent'   => 100,
                'account_id'       => $account->id,
                'company_id'       => static::company()->id,
            ]);
        }

        return $tax->refresh();
    }

    public static function priceList(array $overrides = []): PriceList
    {
        return PriceList::create(array_merge([
            'name'        => 'Counter Prices',
            'currency_id' => static::company()->currency_id,
            'company_id'  => static::company()->id,
            'is_active'   => true,
        ], $overrides));
    }

    public static function priceListItem(PriceList $priceList, Product $product, array $overrides = []): PriceRuleItem
    {
        return PriceRuleItem::create(array_merge([
            'price_list_id'    => $priceList->id,
            'product_id'       => $product->id,
            'apply_to'         => PriceRuleApplyTo::PRODUCT,
            'display_apply_to' => PriceRuleApplyTo::PRODUCT->value,
            'base'             => PriceRuleBase::LIST_PRICE,
            'type'             => PriceRuleType::FIXED,
            'min_quantity'     => 0,
            'currency_id'      => static::company()->currency_id,
            'company_id'       => static::company()->id,
        ], $overrides));
    }

    public static function posCategory(array $overrides = []): Category
    {
        return Category::create(array_merge([
            'name' => 'Kitchen',
        ], $overrides));
    }

    public static function floor(array $overrides = []): Floor
    {
        return Floor::create(array_merge([
            'name' => 'Main Floor',
        ], $overrides));
    }

    public static function table(Floor $floor, array $overrides = []): Table
    {
        return Table::create(array_merge([
            'floor_id'     => $floor->id,
            'table_number' => '1',
            'seats'        => 4,
        ], $overrides));
    }

    public static function quantityOnHand(int $productId, Location $location): float
    {
        return (float) ProductQuantity::withoutGlobalScopes()
            ->where('product_id', $productId)
            ->where('location_id', $location->id)
            ->sum('quantity');
    }

    public static function isCashType(PaymentMethod $paymentMethod): bool
    {
        return $paymentMethod->type === PaymentMethodType::CASH;
    }
}
