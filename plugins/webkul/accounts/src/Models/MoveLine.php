<?php

namespace Webkul\Account\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Webkul\Account\Enums\JournalType;
use Webkul\Account\Enums\DisplayType;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\AccountType;
use Webkul\Account\Models\Product;
use Webkul\Partner\Models\Partner;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;
use Webkul\Support\Models\UOM;
use Webkul\Account\Facades\Tax as TaxFacade;

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
        'created_by',
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
        'date_maturity' => 'date',
        'invoice_date' => 'date',
        'analytic_distribution' => 'array',
        'parent_state' => MoveState::class,
        'display_type' => DisplayType::class,
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

    public function createdBy()
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

    public function fullReconcile()
    {
        return $this->belongsTo(FullReconcile::class);
    }

    public function getTermKeyAttribute()
    {
        if ($this->display_type === DisplayType::PAYMENT_TERM) {
            return [
                'move_id' => $this->move_id,
                'date_maturity' => $this->date_maturity ? $this->date_maturity->toDateString() : null,
                'discount_date' => $this->discount_date,
            ];
        }

        return null;
    }

    /**
     * Bootstrap any application services.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($moveLine) {
            $moveLine->move_name = $moveLine->move->name;

            $moveLine->company_id = $moveLine->move->company_id;

            $moveLine->parent_state = $moveLine->move->state;

            $moveLine->partner_id = $moveLine->move->commercial_partner_id;

            $moveLine->journal_id = $moveLine->move->journal_id;

            $moveLine->company_currency_id = $moveLine->move->company->currency_id;

            $moveLine->computeDateMaturity();

            $moveLine->computeDiscountDate();

            $moveLine->computeUOMId();

            $moveLine->computeCurrencyId();

            $moveLine->computeAccountId();

            $moveLine->computeDisplayType();

            $moveLine->computeName();
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
            $termLines = $termByMove->get($this->move->id, collect());

            $nTerms = $this->move->invoicePaymentTerm?->dueTerms->count() ?? 0;
            
            if ($this->move->payment_reference && $this->move->ref && $this->move->payment_reference !== $this->move->ref) {
                $name = "{$this->move->ref} - {$this->move->payment_reference}";
            } else {
                $name = $this->move->payment_reference ?? '';
            }

            if ($nTerms > 1) {
                $index = $termLines->search(function ($line) {
                    return $line->id === $this->id;
                });

                $index = $index !== false ? $index : $termLines->count();

                $number = $index + 1;

                $name = ltrim("{$name}s installment #{$number}s");
            }
            
            $originalName = $this->getOriginal('name');
            $originalMoveRef = $this->move->getOriginal('ref');
            $originalMovePaymentRef = $this->move->getOriginal('payment_reference');
            
            $shouldUpdateName = $nTerms > 1 
                || !$this->name 
                || $originalName === $originalMovePaymentRef
                || ($originalMovePaymentRef && $originalMoveRef && $originalName === "{$originalMoveRef} - {$originalMovePaymentRef}");
            
            if ($shouldUpdateName) {
                $this->name = $name;
            }
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

        if (!$this->name || $originalName === $originalGetName) {
            $this->name = $getName($this);
        }
    }

    public function computeAccountId()
    {
        $accountId = null;

        switch ($this->display_type) {
            case DisplayType::PAYMENT_TERM:
                $existingAccount = MoveLine::where('move_id', $this->move->id)
                    ->where('display_type', DisplayType::PAYMENT_TERM)
                    ->whereNotNull('account_id')
                    ->value('account_id');

                if ($existingAccount) {
                    $accountId = $existingAccount;
                } else {
                    $isSale = $this->move->isSaleDocument(true);

                    $accountType = $isSale ? 'asset_receivable' : 'liability_payable';

                    $propertyField = $isSale ? 'property_account_receivable_id' : 'property_account_payable_id';

                    $accountId = $this->move->partner?->{$propertyField}
                        ?? (method_exists($this->move->company, 'partner') ? $this->move->company->partner?->{$propertyField} : null)
                        ?? Account::where('account_type', $accountType)->where('deprecated', false)->value('id');

                    if ($this->move->fiscal_position_id && $accountId) {
                        $fiscalPosition = FiscalPosition::find($this->move->fiscal_position_id);

                        if ($fiscalPosition) {
                            // TODO: Implement fiscal position account mapping from fiscal_position_accounts table
                            $accountId = $accountId; // Placeholder for actual mapping
                        }
                    }
                }
                break;

            case DisplayType::PRODUCT:
                if ($this->product_id && $this->product) {
                    if ($this->move->isSaleDocument(true)) {
                        $accountId = $this->product->property_account_income_id ?? $this->product->category?->property_account_income_id;
                    } elseif ($this->move->isPurchaseDocument(true)) {
                        $accountId = $this->product->property_account_expense_id ?? $this->product->category?->property_account_expense_id;
                    }

                    if ($this->move->fiscal_position_id && $accountId) {
                        $fiscalPosition = FiscalPosition::find($this->move->fiscal_position_id);

                        if ($fiscalPosition) {
                            // TODO: Implement fiscal position account mapping from fiscal_position_accounts table
                            $accountId = $accountId; // Placeholder for actual mapping
                        }
                    }
                } elseif ($this->partner_id) {
                    $accountType = $this->move->isSaleDocument(true) ? AccountType::INCOME : AccountType::EXPENSE;

                    $accountId = Account::where('account_type', $accountType)->where('deprecated', false)->value('id');
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
                    ->pluck('account_id');

                if ($previousAccounts->count() === 1 && $this->move->lines()->count() > 2) {
                    $accountId = $previousAccounts->first();
                } else {
                    $accountId = $this->move->journal?->default_account_id;
                }

                break;
        }

        $this->account_id = $accountId;
    }

    public function computeDisplayType()
    {
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

    public function computeDateMaturity()
    {
        //Todo: Should be computed based on payment terms
        $this->date_maturity = $this->move->invoice_date_due;
    }

    public function computeDiscountDate()
    {
        //Todo: Should be computed based on early payment discounts
    }

    public function computeUOMId()
    {
        if ($this->move->isPurchaseDocument()) {
            $this->uom_id = $this->uom_id ?? $this->product?->uom_po_id;
        } else {
            $this->uom_id = $this->uom_id ?? $this->product?->uom_id;
        }
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

    public function computeBalance()
    {
        if (in_array($this->display_type, [DisplayType::LINE_SECTION, DisplayType::LINE_NOTE])) {
            $this->balance = 0.0;
        } elseif (! $this->move->isInvoice(true)) {
            // TODO: Handle other move types if needed
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
            $this->amount_currency = round($this->balance * $this->move->currency_rate, 2);
        }

        if ($this->currency_id === $this->company->currency_id) {
            $this->amount_currency = $this->balance;
        }
    }
}
