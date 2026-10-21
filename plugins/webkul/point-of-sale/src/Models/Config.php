<?php

namespace Webkul\PointOfSale\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Throwable;
use Webkul\Account\Enums\AccountType;
use Webkul\Account\Enums\JournalType;
use Webkul\Account\Models\Account;
use Webkul\Account\Models\CashRounding;
use Webkul\Account\Models\FiscalPosition;
use Webkul\Account\Models\Journal;
use Webkul\Field\Traits\HasCustomFields;
use Webkul\Inventory\Models\OperationType;
use Webkul\Inventory\Models\Route;
use Webkul\Inventory\Models\Warehouse;
use Webkul\PointOfSale\Database\Factories\ConfigFactory;
use Webkul\PointOfSale\Enums\PickingPolicy;
use Webkul\PointOfSale\Enums\SessionState;
use Webkul\PointOfSale\Enums\TaxDisplay;
use Webkul\PointOfSale\Models\Warehouse as PointOfSaleWarehouse;
use Webkul\PointOfSale\Services\PaymentMethodProvisioner;
use Webkul\PointOfSale\Services\RestaurantFloorProvisioner;
use Webkul\PointOfSale\Settings\AccountSettings;
use Webkul\Product\Models\PriceList;
use Webkul\Product\Models\Product;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;
use Webkul\Support\Models\Scopes\CompanyScope;
use Webkul\Support\Models\Sequence;
use Webkul\Support\Services\SequenceService;
use Webkul\Support\Traits\BelongsToCompany;
use Webkul\Support\Traits\ChecksCompanyConsistency;

class Config extends Model implements Sortable
{
    use BelongsToCompany, ChecksCompanyConsistency, HasCustomFields, HasFactory, SoftDeletes, SortableTrait;

    public const SESSION_SEQUENCE_VARIANT = 'session';

    public const TERMINAL_JOURNAL_CODE = 'POSS';

    protected $table = 'pos_configs';

    protected $fillable = [
        'name',
        'code',
        'access_token',
        'sort',
        'tax_display',
        'picking_policy',
        'receipt_header',
        'receipt_footer',
        'limited_products_amount',
        'amount_authorized_diff',
        'is_active',
        'is_restaurant',
        'is_closing_entry_by_product',
        'enable_maximum_difference',
        'enable_line_discount',
        'enable_global_discount',
        'global_discount_percentage',
        'enable_price_control',
        'enable_customer_required',
        'enable_receipt_print',
        'enable_receipt_auto_print',
        'enable_price_list',
        'enable_fiscal_position',
        'enable_tip',
        'enable_ship_later',
        'enable_cash_rounding',
        'enable_only_round_cash_method',
        'enable_cogs',
        'enable_split_bill',
        'enable_print_bill',
        'enable_takeaway',
        'limit_categories',
        'show_product_images',
        'show_category_images',
        'show_margins_and_costs',
        'warehouse_id',
        'operation_type_id',
        'return_operation_type_id',
        'ship_later_route_id',
        'journal_id',
        'invoice_journal_id',
        'cogs_journal_id',
        'receivable_account_id',
        'stock_output_account_id',
        'balancing_account_id',
        'cash_movement_account_id',
        'cash_rounding_id',
        'price_list_id',
        'fiscal_position_id',
        'takeaway_fiscal_position_id',
        'discount_product_id',
        'tip_product_id',
        'currency_id',
        'company_id',
        'creator_id',
    ];

    protected $casts = [
        'tax_display'                   => TaxDisplay::class,
        'picking_policy'                => PickingPolicy::class,
        'amount_authorized_diff'        => 'decimal:4',
        'is_active'                     => 'boolean',
        'is_restaurant'                 => 'boolean',
        'is_closing_entry_by_product'   => 'boolean',
        'enable_maximum_difference'     => 'boolean',
        'enable_line_discount'          => 'boolean',
        'enable_global_discount'        => 'boolean',
        'global_discount_percentage'    => 'decimal:2',
        'enable_price_control'          => 'boolean',
        'enable_customer_required'      => 'boolean',
        'enable_receipt_print'          => 'boolean',
        'enable_receipt_auto_print'     => 'boolean',
        'enable_price_list'             => 'boolean',
        'enable_fiscal_position'        => 'boolean',
        'enable_tip'                    => 'boolean',
        'enable_ship_later'             => 'boolean',
        'enable_cash_rounding'          => 'boolean',
        'enable_only_round_cash_method' => 'boolean',
        'enable_cogs'                   => 'boolean',
        'enable_split_bill'             => 'boolean',
        'enable_print_bill'             => 'boolean',
        'enable_takeaway'               => 'boolean',
        'limit_categories'              => 'boolean',
        'show_product_images'           => 'boolean',
        'show_category_images'          => 'boolean',
        'show_margins_and_costs'        => 'boolean',
    ];

    protected $attributes = [
        'tax_display'                   => 'subtotal',
        'picking_policy'                => 'direct',
        'limited_products_amount'       => 500,
        'is_active'                     => true,
        'is_restaurant'                 => false,
        'is_closing_entry_by_product'   => false,
        'enable_maximum_difference'     => false,
        'enable_line_discount'          => true,
        'enable_global_discount'        => false,
        'global_discount_percentage'    => 10,
        'enable_price_control'          => false,
        'enable_customer_required'      => false,
        'enable_receipt_print'          => true,
        'enable_receipt_auto_print'     => false,
        'enable_price_list'             => false,
        'enable_fiscal_position'        => false,
        'enable_tip'                    => false,
        'enable_ship_later'             => false,
        'enable_cash_rounding'          => false,
        'enable_only_round_cash_method' => false,
        'enable_cogs'                   => false,
        'enable_split_bill'             => false,
        'enable_print_bill'             => false,
        'enable_takeaway'               => false,
        'limit_categories'              => false,
        'show_product_images'           => true,
        'show_category_images'          => true,
        'show_margins_and_costs'        => true,
    ];

    public $sortable = [
        'order_column_name'  => 'sort',
        'sort_when_creating' => true,
    ];

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function operationType(): BelongsTo
    {
        return $this->belongsTo(OperationType::class)->withTrashed();
    }

    public function returnOperationType(): BelongsTo
    {
        return $this->belongsTo(OperationType::class, 'return_operation_type_id')->withTrashed();
    }

    public function shipLaterRoute(): BelongsTo
    {
        return $this->belongsTo(Route::class, 'ship_later_route_id')->withTrashed();
    }

    public function journal(): BelongsTo
    {
        return $this->belongsTo(Journal::class);
    }

    public function invoiceJournal(): BelongsTo
    {
        return $this->belongsTo(Journal::class, 'invoice_journal_id');
    }

    public function cogsJournal(): BelongsTo
    {
        return $this->belongsTo(Journal::class, 'cogs_journal_id');
    }

    public function receivableAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'receivable_account_id');
    }

    public function stockOutputAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'stock_output_account_id');
    }

    public function balancingAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'balancing_account_id');
    }

    public function cashMovementAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'cash_movement_account_id');
    }

    public function cashRounding(): BelongsTo
    {
        return $this->belongsTo(CashRounding::class);
    }

    public function priceList(): BelongsTo
    {
        return $this->belongsTo(PriceList::class);
    }

    public function fiscalPosition(): BelongsTo
    {
        return $this->belongsTo(FiscalPosition::class);
    }

    public function takeawayFiscalPosition(): BelongsTo
    {
        return $this->belongsTo(FiscalPosition::class, 'takeaway_fiscal_position_id');
    }

    public function discountProduct(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'discount_product_id');
    }

    public function tipProduct(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'tip_product_id');
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(Session::class);
    }

    public function lastClosedSession(): HasOne
    {
        return $this->hasOne(Session::class)
            ->where('state', SessionState::CLOSED)
            ->latest('stopped_at');
    }

    public function cashPaymentMethod(): ?PaymentMethod
    {
        return $this->paymentMethods()
            ->where('is_cash_count', true)
            ->orderBy('pos_payment_methods.sort')
            ->orderBy('pos_payment_methods.id')
            ->first();
    }

    public function paymentMethods(): BelongsToMany
    {
        return $this->belongsToMany(PaymentMethod::class, 'pos_config_payment_methods', 'config_id', 'payment_method_id');
    }

    public function priceLists(): BelongsToMany
    {
        return $this->belongsToMany(PriceList::class, 'pos_config_price_lists', 'config_id', 'price_list_id');
    }

    public function floors(): BelongsToMany
    {
        return $this->belongsToMany(Floor::class, 'pos_config_floors', 'config_id', 'floor_id');
    }

    public function printers(): BelongsToMany
    {
        return $this->belongsToMany(Printer::class, 'pos_config_printers', 'config_id', 'printer_id');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'pos_config_categories', 'config_id', 'category_id');
    }

    /**
     * @return array<int, int>
     */
    public function availableCategoryIds(): array
    {
        if (! $this->limit_categories) {
            return [];
        }

        $ids = $this->categories->pluck('id')->all();

        if ($ids === []) {
            return [];
        }

        $pending = $ids;

        while ($pending !== []) {
            $children = Category::query()
                ->whereIn('parent_id', $pending)
                ->whereNotIn('id', $ids)
                ->pluck('id')
                ->all();

            if ($children === []) {
                break;
            }

            $ids = array_merge($ids, $children);

            $pending = $children;
        }

        return $ids;
    }

    public function bills(): BelongsToMany
    {
        return $this->belongsToMany(Bill::class, 'pos_config_bills', 'config_id', 'bill_id');
    }

    public function fiscalPositions(): BelongsToMany
    {
        return $this->belongsToMany(FiscalPosition::class, 'pos_config_fiscal_positions', 'config_id', 'fiscal_position_id');
    }

    public function companyConsistentFields(): array
    {
        return [
            'warehouse_id'      => Warehouse::class,
            'operation_type_id' => OperationType::class,
            'journal_id'        => Journal::class,
        ];
    }

    public function sequenceDefaults(string $variant = ''): array
    {
        if ($variant === self::SESSION_SEQUENCE_VARIANT) {
            return [
                'name'   => "{$this->name} Sessions",
                'prefix' => "{$this->code}/SESSION/",
            ];
        }

        return [
            'name'   => $this->name,
            'prefix' => "{$this->code}/",
        ];
    }

    public function ensureSequences(): void
    {
        if (! Schema::hasTable('sequences')) {
            return;
        }

        SequenceService::ensureFor($this, '', $this->company_id, $this->sequenceDefaults());

        SequenceService::ensureFor($this, self::SESSION_SEQUENCE_VARIANT, $this->company_id, $this->sequenceDefaults(self::SESSION_SEQUENCE_VARIANT));
    }

    public function syncSequences(): void
    {
        if (! Schema::hasTable('sequences')) {
            return;
        }

        Sequence::withoutGlobalScope(CompanyScope::class)
            ->where('scope_type', $this->getMorphClass())
            ->where('scope_id', $this->id)
            ->get()
            ->each(function (Sequence $sequence) {
                $defaults = $this->sequenceDefaults((string) $sequence->variant);

                $sequence->update([
                    'name'   => $defaults['name'],
                    'prefix' => $defaults['prefix'],
                ]);
            });
    }

    public function computeCreatorId(): void
    {
        $this->creator_id ??= Auth::id();
    }

    public function computeCode(): void
    {
        $this->code = Str::upper($this->code ?: Str::slug($this->name, ''));
    }

    public function computeAccessToken(): void
    {
        $this->access_token ??= Str::uuid()->toString();
    }

    public function computeCurrencyId(): void
    {
        $this->currency_id ??= $this->journal?->currency_id ?? $this->company?->currency_id;
    }

    public function computeWarehouseId(): void
    {
        $this->warehouse_id ??= Warehouse::query()
            ->where(owned_by_company($this->company_id))
            ->orderBy('id')
            ->value('id');
    }

    public function computeOperationTypeIds(): void
    {
        $warehouse = $this->warehouse_id
            ? PointOfSaleWarehouse::withoutGlobalScopes()->find($this->warehouse_id)
            : null;

        if ($warehouse && (! $warehouse->pos_type_id || ! $warehouse->pos_return_type_id)) {
            $warehouse->handlePosWarehouseCreation();

            $warehouse->finalizePosWarehouseCreation();
        }

        $this->operation_type_id ??= $warehouse?->pos_type_id;

        $this->return_operation_type_id ??= $warehouse?->pos_return_type_id;
    }

    public function ensureDefaultPaymentMethods(): void
    {
        app(PaymentMethodProvisioner::class)->ensureFor($this);
    }

    public function usesFiscalPositions(): bool
    {
        return (bool) $this->enable_fiscal_position
            || ((bool) $this->is_restaurant && (bool) $this->enable_takeaway);
    }

    public function allowedFiscalPositionIds(): array
    {
        if (! $this->usesFiscalPositions()) {
            return [];
        }

        $ids = [$this->fiscal_position_id];

        if ($this->enable_fiscal_position) {
            $ids = array_merge($ids, $this->fiscalPositions()->pluck('accounts_fiscal_positions.id')->all());
        }

        if ($this->is_restaurant && $this->enable_takeaway) {
            $ids[] = $this->takeaway_fiscal_position_id;
        }

        return array_values(array_unique(array_filter(array_map('intval', $ids))));
    }

    public function syncRestaurantFloors(): void
    {
        app(RestaurantFloorProvisioner::class)->syncFor($this);
    }

    public function computeReceivableAccountId(): void
    {
        if ($this->receivable_account_id) {
            return;
        }

        $accounts = fn (): Builder => Account::query()
            ->where('account_type', AccountType::ASSET_RECEIVABLE)
            ->where('reconcile', true)
            ->where(fn (Builder $query) => $query->whereNull('deprecated')->orWhere('deprecated', false))
            ->where(owned_by_company($this->company_id));

        $settingAccountId = $this->settingReceivableAccountId();

        $this->receivable_account_id = ($settingAccountId ? $accounts()->whereKey($settingAccountId)->value('id') : null)
            ?? $accounts()->orderBy('id')->value('id');
    }

    protected function settingReceivableAccountId(): ?int
    {
        try {
            return settings(AccountSettings::class)->receivable_account_id;
        } catch (Throwable) {
            return null;
        }
    }

    public function computeJournalIds(): void
    {
        $this->discardJournalOfWrongType('journal_id', JournalType::GENERAL, JournalType::SALE);

        $this->discardJournalOfWrongType('invoice_journal_id', JournalType::SALE);

        $this->journal_id ??= $this->terminalJournal()?->id;

        $this->invoice_journal_id ??= Journal::query()
            ->where('type', JournalType::SALE)
            ->where(owned_by_company($this->company_id))
            ->orderBy('id')
            ->value('id');

        $this->unsetRelation('journal');

        $this->unsetRelation('invoiceJournal');
    }

    protected function discardJournalOfWrongType(string $attribute, JournalType ...$expected): void
    {
        if (! $this->{$attribute}) {
            return;
        }

        $type = Journal::query()->whereKey($this->{$attribute})->value('type');

        if ($type instanceof JournalType) {
            $type = $type->value;
        }

        $allowed = array_map(fn (JournalType $journalType): string => $journalType->value, $expected);

        if (! in_array($type, $allowed, true)) {
            $this->{$attribute} = null;
        }
    }

    protected function terminalJournal(): ?Journal
    {
        $journal = Journal::query()
            ->where('type', JournalType::GENERAL)
            ->where('code', self::TERMINAL_JOURNAL_CODE)
            ->where(owned_by_company($this->company_id))
            ->orderBy('id')
            ->first();

        if ($journal || ! $this->company_id) {
            return $journal;
        }

        return Journal::create([
            'name'       => __('point-of-sale::system.config.terminal-journal'),
            'code'       => self::TERMINAL_JOURNAL_CODE,
            'type'       => JournalType::GENERAL,
            'company_id' => $this->company_id,
        ]);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function (Config $config) {
            $config->computeCreatorId();

            $config->computeAccessToken();

            $config->computeWarehouseId();

            $config->computeOperationTypeIds();

            $config->computeJournalIds();

            $config->computeReceivableAccountId();
        });

        static::saving(function (Config $config) {
            $config->computeCode();

            $config->computeJournalIds();

            $config->computeCurrencyId();
        });

        static::created(function (Config $config) {
            $config->ensureSequences();

            $config->ensureDefaultPaymentMethods();
        });

        static::updated(function (Config $config) {
            if ($config->wasChanged(['name', 'code'])) {
                $config->syncSequences();
            }
        });
    }

    protected static function newFactory(): ConfigFactory
    {
        return ConfigFactory::new();
    }
}
