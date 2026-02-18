<?php

namespace Webkul\Account\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Webkul\Account\Enums\AccountType;
use Webkul\Account\Enums\DisplayType;
use Webkul\Account\Enums\JournalType;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Enums\TypeTaxUse;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;
use Webkul\Support\Models\UOM;

class MoveLine extends Model implements Sortable
{
    use HasFactory, SortableTrait;

    protected $table = 'accounts_account_move_lines';

    protected $fillable = [
        'sort',
        'move_id',
        'journal_id',
        'company_id',
        'company_currency_id',
        'reconcile_id',
        'payment_id',
        'tax_repartition_line_id',
        'account_id',
        'currency_id',
        'partner_id',
        'group_tax_id',
        'tax_line_id',
        'tax_group_id',
        'statement_id',
        'statement_line_id',
        'product_id',
        'uom_id',
        'creator_id',
        'move_name',
        'parent_state',
        'reference',
        'name',
        'matching_number',
        'display_type',
        'date',
        'invoice_date',
        'date_maturity',
        'discount_date',
        'analytic_distribution',
        'debit',
        'credit',
        'balance',
        'amount_currency',
        'tax_base_amount',
        'amount_residual',
        'amount_residual_currency',
        'quantity',
        'price_unit',
        'price_subtotal',
        'price_total',
        'discount',
        'discount_amount_currency',
        'discount_balance',
        'is_imported',
        'tax_tag_invert',
        'reconciled',
        'is_downpayment',
        'full_reconcile_id',
    ];

    protected $casts = [
        'date'                  => 'date',
        'date_maturity'         => 'date',
        'invoice_date'          => 'date',
        'discount_date'         => 'date',
        'analytic_distribution' => 'array',
        'parent_state'          => MoveState::class,
        'display_type'          => DisplayType::class,
    ];

    public $sortable = [
        'order_column_name'  => 'sort',
        'sort_when_creating' => true,
    ];

    public function move()
    {
        return $this->belongsTo(Move::class);
    }

    public function journal()
    {
        return $this->belongsTo(Journal::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function companyCurrency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function groupTax()
    {
        return $this->belongsTo(Tax::class);
    }

    public function taxes()
    {
        return $this->belongsToMany(Tax::class, 'accounts_accounts_move_line_taxes', 'move_line_id', 'tax_id');
    }

    public function taxGroup()
    {
        return $this->belongsTo(TaxGroup::class);
    }

    public function statement()
    {
        return $this->belongsTo(BankStatement::class);
    }

    public function statementLine()
    {
        return $this->belongsTo(BankStatementLine::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function uom()
    {
        return $this->belongsTo(UOM::class, 'uom_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function moveLines()
    {
        return $this->hasMany(MoveLine::class, 'reconcile_id');
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function taxRepartitionLine()
    {
        return $this->belongsTo(TaxPartition::class, 'tax_repartition_line_id');
    }

    public function fullReconcile()
    {
        return $this->belongsTo(FullReconcile::class);
    }

    public function matchedDebits()
    {
        return $this->hasMany(PartialReconcile::class, 'credit_move_id');
    }

    public function matchedCredits()
    {
        return $this->hasMany(PartialReconcile::class, 'debit_move_id');
    }

    public function getTermKeyAttribute()
    {
        if ($this->display_type === DisplayType::PAYMENT_TERM) {
            return [
                'move_id'       => $this->move_id,
                'date_maturity' => $this->date_maturity?->toDateString(),
                'discount_date' => $this->discount_date,
            ];
        }

        return null;
    }

    public function getIsRefundAttribute()
    {
        $isRefund = false;

        if (in_array($this->move->move_type, [MoveType::OUT_REFUND, MoveType::IN_REFUND])) {
            $isRefund = true;
        } elseif ($this->move->move_type == MoveType::ENTRY) {
            if ($this->taxRepartitionLine) {
                $isRefund = $this->taxRepartitionLine->document_type == 'refund';
            } else {
                $tax = $this->taxes->first();
                $taxType = $tax?->type_tax_use;

                if ($taxType == TypeTaxUse::SALE && $this->credit == 0.0) {
                    $isRefund = true;
                } elseif ($taxType == TypeTaxUse::PURCHASE && $this->debit == 0.0) {
                    $isRefund = true;
                }

                if ($this->taxes->isNotEmpty() && $this->move->reversed_entry_id) {
                    $isRefund = ! $isRefund;
                }
            }
        }

        return $isRefund;
    }

    public function getPaymentDateAttribute()
    {
        if ($this->discount_date && today()->toDateString() <= $this->discount_date->toDateString()) {
            return $this->discount_date;
        }

        return $this->date_maturity;
    }

    /**
     * Bootstrap any application services.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($moveLine) {
            $moveLine->creator_id ??= Auth::id();
        });

        static::saving(function ($moveLine) {
            $moveLine->move_name = $moveLine->move->name;

            $moveLine->company_id = $moveLine->move->company_id;

            $moveLine->parent_state = $moveLine->move->state;

            $moveLine->partner_id = $moveLine->move->commercial_partner_id;

            $moveLine->journal_id = $moveLine->move->journal_id;

            $moveLine->company_currency_id = $moveLine->move->company->currency_id;

            $moveLine->date = $moveLine->move->date;

            $moveLine->computeUOMId();

            $moveLine->computeCurrencyId();

            $moveLine->computePaymentId();

            $moveLine->computeAccountId();

            $moveLine->computeDisplayType();

            $moveLine->computeName();

            $moveLine->computeTaxTagInvert();
        });
    }

    public function computeName()
    {
        $getName = function ($line) {
            $values = [];

            if (! $line->product) {
                return false;
            }

            if ($line->journal->type === JournalType::SALE) {
                $values[] = $line->product->display_name;

                if ($line->product->description_sale) {
                    $values[] = $line->product->description_sale;
                }
            } elseif ($line->journal->type === JournalType::PURCHASE) {
                $values[] = $line->product->display_name;

                if ($line->product->description_purchase) {
                    $values[] = $line->product->description_purchase;
                }
            }

            return implode("\n", $values);
        };

        $allLines = $this->move->lines->merge(collect([$this]));

        $paymentTermLines = $allLines->filter(function ($l) {
            return $l->display_type === DisplayType::PAYMENT_TERM;
        })->sortBy(function ($l) {
            return $l->date_maturity ?? '9999-12-31';
        });

        $termByMove = $paymentTermLines->groupBy('move_id');

        if ($this->move->inalterable_hash !== false && $this->move->inalterable_hash !== null) {
            return;
        }

        if ($this->display_type === DisplayType::PAYMENT_TERM) {
            $move = $this->move()->with('invoicePaymentTerm.dueTerms')->first();
            $nTerms = $move?->invoicePaymentTerm?->dueTerms?->count() ?? 0;

            $baseName = $move->payment_reference ?? $move->ref ?? $move->name ?? 'Payment Term';

            if ($nTerms <= 1) {
                $this->name = $baseName;

                return;
            }

            $termLines = $termByMove->get($this->move->id, collect());
            $index = $termLines->search(fn ($line) => $line->id === $this->id) ?: 0;
            $number = $index + 1;

            $this->name = "{$baseName} - Installment #{$number}";
        }

        if (! $this->product_id || in_array($this->display_type, [DisplayType::LINE_SECTION, DisplayType::LINE_NOTE])) {
            return;
        }

        $originalName = $this->getOriginal('name');

        $originalGetName = false;

        if ($this->exists) {
            $originalLine = clone $this;

            $originalLine->setRawAttributes($this->getOriginal());

            $originalGetName = $getName($originalLine);
        }

        if (! $this->name || $originalName === $originalGetName) {
            $this->name = $getName($this);
        }
    }

    public function computePaymentId()
    {
        $this->payment_id = $this->move->origin_payment_id;
    }

    public function computeAccountId()
    {
        if ($this->payment_id || $this->tax_line_id || $this->display_type == DisplayType::ROUNDING) {
            return;
        }

        $accountId = null;

        switch ($this->display_type) {
            case DisplayType::PAYMENT_TERM:
                $isSale = $this->move->isSaleDocument(true);

                $accountType = $isSale ? AccountType::ASSET_RECEIVABLE : AccountType::LIABILITY_PAYABLE;

                $propertyField = $isSale ? 'propertyAccountReceivable' : 'propertyAccountPayable';

                $account = $this->move->partner?->{$propertyField}
                    ?? (method_exists($this->move->company, 'partner') ? $this->move->company->partner?->{$propertyField} : null)
                    ?? Account::where('account_type', $accountType)->where('deprecated', false)->first();

                if ($this->move->fiscalPosition && $account) {
                    $account = $this->move->fiscalPosition->mapAccount($account);
                }

                $accountId = $account?->id;

                break;

            case DisplayType::PRODUCT:
                if ($this->product) {
                    $accounts = $this->product->getAccountsFromFiscalPosition($this->move->fiscalPosition);

                    if ($this->move->isSaleDocument(true)) {
                        $account = $accounts['income'] ?? $this->account;
                    } elseif ($this->move->isPurchaseDocument(true)) {
                        $account = $accounts['expense'] ?? $this->account;
                    }

                    $accountId = $account?->id;
                } elseif ($this->partner) {
                    $accountId = $this->account_id ?? (new Account)->getMostFrequentAccountsForPartner(
                        companyId: $this->move->company_id,
                        partnerId: $this->partner_id,
                        moveType: $this->move->type,
                    );
                }

                break;

            case DisplayType::LINE_SECTION:
            case DisplayType::LINE_NOTE:
                // These don't need accounts
                break;

            default:
                $previousAccounts = MoveLine::where('move_id', $this->move->id)
                    ->where('display_type', $this->display_type)
                    ->whereNotNull('account_id')
                    ->orderBy('id', 'desc')
                    ->limit(2)
                    ->get();

                $accountId = $previousAccounts->count() === 1 && $this->move->lines()->count() > 2
                    ? $previousAccounts->first()?->account_id
                    : $this->move->journal?->default_account_id;

                break;
        }

        $this->account_id = $accountId ?? $this->account_id;
    }

    public function computeDisplayType()
    {
        if ($this->display_type) {
            return;
        }

        if ($this->move->isInvoice()) {
            if ($this->tax_line_id) {
                $this->display_type = DisplayType::TAX;
            } elseif (in_array($this->account->account_type, [AccountType::ASSET_RECEIVABLE, AccountType::LIABILITY_PAYABLE])) {
                $this->display_type = DisplayType::PAYMENT_TERM;
            } else {
                $this->display_type = DisplayType::PRODUCT;
            }
        } else {
            $this->display_type = DisplayType::PRODUCT;
        }
    }

    public function computeUOMId()
    {
        if ($this->uom_id) {
            return;
        }

        $this->uom_id = $this->move->isPurchaseDocument()
            ? $this->product?->uom_po_id
            : $this->product?->uom_id;
    }

    public function computeCurrencyId()
    {
        if ($this->display_type === DisplayType::COGS) {
        } elseif ($this->move->isInvoice(true)) {
            $this->currency_id = $this->move->currency_id;
        } else {
            $this->currency_id = $this->currency_id ?? $this->company_currency_id;
        }
    }

    public function computeTaxTagInvert()
    {
        $this->tax_tag_invert = true;

        $originMove = $this->move->tax_cash_basis_origin_move_id ?: $this->move;

        if (! $this->tax_repartition_line_id && $this->taxes->isEmpty()) {
            $this->tax_tag_invert = $this->taxTags?->isNotEmpty() && $originMove->isInbound();
        } elseif ($originMove->move_type == MoveType::ENTRY) {
            $tax = $this->taxRepartitionLine->tax ?? $this->taxes->first();

            if ($this->display_type == DisplayType::EPD) {
                $this->tax_tag_invert = $tax->type_tax_use == TypeTaxUse::PURCHASE;
            } else {
                $this->tax_tag_invert = (
                    $tax->type_tax_use == TypeTaxUse::PURCHASE
                    && $this->is_refund
                )
                    || (
                        $tax->type_tax_use == TypeTaxUse::SALE
                        && ! $this->is_refund
                    );
            }
        } else {
            $this->tax_tag_invert = $originMove->isInbound();
        }
    }

    public function computeBalance()
    {
        if (in_array($this->display_type, [DisplayType::LINE_SECTION, DisplayType::LINE_NOTE])) {
            $this->balance = 0.0;
        } elseif (! $this->move->isInvoice(true)) {
            $this->balance = $this->debit - $this->credit;
        } else {
            $this->balance = $this->balance;
        }
    }

    public function computeCreditAndDebit()
    {
        if (! $this->move->is_storno) {
            $this->debit = $this->balance > 0.0 ? $this->balance : 0.0;
            $this->credit = $this->balance < 0.0 ? -$this->balance : 0.0;
        } else {
            $this->debit = $this->balance < 0.0 ? $this->balance : 0.0;
            $this->credit = $this->balance > 0.0 ? -$this->balance : 0.0;
        }
    }

    public function computeAmountCurrency()
    {
        if (is_null($this->amount_currency)) {
            $this->amount_currency = round($this->balance * $this->move->invoice_currency_rate, 2);
        }

        if ($this->currency_id === $this->company->currency_id) {
            $this->amount_currency = $this->balance;
        }
    }

    public function computeAmountResidual()
    {
        $shouldCompute = $this->account->reconcile
            || in_array($this->account->account_type, [
                AccountType::ASSET_CASH,
                AccountType::LIABILITY_CREDIT_CARD,
            ]);

        if (! $shouldCompute) {
            $this->amount_residual = 0.0;
            $this->amount_residual_currency = 0.0;
            $this->reconciled = false;

            return $this;
        }

        $debit = PartialReconcile::select(
            DB::raw('COALESCE(SUM(amount), 0) AS amount'),
            DB::raw('COALESCE(SUM(debit_amount_currency), 0) AS amount_currency'),
            'currencies.decimal_places'
        )
            ->join('currencies', 'currencies.id', '=', 'accounts_partial_reconciles.debit_currency_id')
            ->where('debit_move_id', $this->id)
            ->groupBy('currencies.decimal_places')
            ->first();

        $credit = PartialReconcile::select(
            DB::raw('COALESCE(SUM(amount), 0) AS amount'),
            DB::raw('COALESCE(SUM(credit_amount_currency), 0) AS amount_currency'),
            'currencies.decimal_places'
        )
            ->join('currencies', 'currencies.id', '=', 'accounts_partial_reconciles.credit_currency_id')
            ->where('credit_move_id', $this->id)
            ->groupBy('currencies.decimal_places')
            ->first();

        $debitAmount = $debit->amount ?? 0.0;

        $debitAmountCurrency = 0.0;

        if (isset($debit->amount_currency)) {
            $decimalPlaces = $debit->decimal_places ?? 2;

            $debitAmountCurrency = round($debit->amount_currency, $decimalPlaces);
        }

        $creditAmount = $credit->amount ?? 0.0;

        $creditAmountCurrency = 0.0;

        if (isset($credit->amount_currency)) {
            $decimalPlaces = $credit->decimal_places ?? 2;

            $creditAmountCurrency = round($credit->amount_currency, $decimalPlaces);
        }

        $companyCurrency = $this->companyCurrency ?? $this->company->currency;

        $foreignCurrency = $this->currency ?? $companyCurrency;

        $this->amount_residual = $companyCurrency->round($this->balance - $debitAmount + $creditAmount);

        $this->amount_residual_currency = $foreignCurrency->round($this->amount_currency - $debitAmountCurrency + $creditAmountCurrency);

        $this->reconciled = $companyCurrency->isZero($this->amount_residual)
            && $foreignCurrency->isZero($this->amount_residual_currency);
    }
}
