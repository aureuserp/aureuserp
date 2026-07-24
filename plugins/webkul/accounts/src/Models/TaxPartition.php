<?php

namespace Webkul\Account\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Webkul\Account\Database\Factories\TaxPartitionFactory;
use Webkul\Account\Enums\DocumentType;
use Webkul\Account\Enums\RepartitionType;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

class TaxPartition extends Model implements Sortable
{
    use HasFactory, SortableTrait;

    protected $table = 'accounts_tax_partition_lines';

    protected $fillable = [
        'account_id',
        'tax_id',
        'company_id',
        'sort',
        'repartition_type',
        'document_type',
        'use_in_tax_closing',
        'factor_percent',
        'creator_id',
    ];

    protected $casts = [
        'document_type'    => DocumentType::class,
        'repartition_type' => RepartitionType::class,
    ];

    public $sortable = [
        'order_column_name'  => 'sort',
        'sort_when_creating' => true,
    ];

    public function getFactorAttribute(): float
    {
        return (float) ($this->factor_percent ?? 0) / 100.0;
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function tax()
    {
        return $this->belongsTo(Tax::class, 'tax_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public static function validateRepartitionLines($taxId): void
    {
        $invoices = self::where('document_type', DocumentType::INVOICE)
            ->where('tax_id', $taxId)
            ->orderBy('sort')
            ->get();

        $refunds = self::where('document_type', DocumentType::REFUND)
            ->where('tax_id', $taxId)
            ->orderBy('sort')
            ->get();

        if ($invoices->count() !== $refunds->count()) {
            throw ValidationException::withMessages([
                'invoice_repartition_lines' => 'Invoice and refund distributions must have the same number of lines for this tax.',
            ]);
        }

        if (
            $invoices->where('repartition_type', RepartitionType::BASE)->count() !== 1 ||
            $refunds->where('repartition_type', RepartitionType::BASE)->count() !== 1
        ) {
            throw ValidationException::withMessages([
                'invoice_repartition_lines' => 'Invoice must contain exactly one BASE repartition line.',
                'refund_repartition_lines'  => 'Refund must contain exactly one BASE repartition line.',
            ]);
        }

        if (
            $invoices->where('repartition_type', RepartitionType::TAX)->isEmpty() ||
            $refunds->where('repartition_type', RepartitionType::TAX)->isEmpty()
        ) {
            throw ValidationException::withMessages([
                'invoice_repartition_lines' => 'Invoice must contain at least one TAX repartition line.',
                'refund_repartition_lines'  => 'Refund must contain at least one TAX repartition line.',
            ]);
        }

        foreach ($invoices as $index => $invoiceLine) {
            $refundLine = $refunds[$index] ?? null;

            if (
                ! $refundLine ||
                $invoiceLine->repartition_type !== $refundLine->repartition_type ||
                (float) $invoiceLine->factor_percent !== (float) $refundLine->factor_percent
            ) {
                throw ValidationException::withMessages([
                    'invoice_repartition_lines' => 'Invoice and refund repartition lines must match in type and percentage order.',
                ]);
            }
        }

        $positive = $invoices->where('factor_percent', '>', 0)->sum('factor_percent');
        $negative = $invoices->where('factor_percent', '<', 0)->sum('factor_percent');

        if (bccomp((string) $positive, '100', 2) !== 0) {
            throw ValidationException::withMessages([
                'invoice_repartition_lines' => 'Total positive factors must equal 100%.',
            ]);
        }

        if ($negative && bccomp((string) $negative, '-100', 2) !== 0) {
            throw ValidationException::withMessages([
                'invoice_repartition_lines' => 'Total negative factors must equal -100%.',
            ]);
        }
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($taxPartition) {
            $taxPartition->creator_id ??= Auth::id();
        });
    }

    protected static function newFactory(): TaxPartitionFactory
    {
        return TaxPartitionFactory::new();
    }
}
