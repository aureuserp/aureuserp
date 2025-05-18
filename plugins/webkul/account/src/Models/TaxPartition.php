<?php

namespace Webkul\Account\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
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

    public $sortable = [
        'order_column_name'  => 'sort',
        'sort_when_creating' => true,
    ];

    public function createdBy()
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

    public static function validateRepartitionLines($invoices, $refunds)
    {
        if ($invoices->count() !== $refunds->count()) {
            throw new Exception('Invoice and credit note distribution should have the same number of records.');
        }

        if ($invoices->where('repartition_type', 'base')->count() !== 1 || $refunds->where('repartition_type', 'base')->count() !== 1) {
            throw new Exception('Invoice and credit note distribution should each contain exactly one record for the base.');
        }

        if (! $invoices->where('repartition_type', 'tax')->count() || ! $refunds->where('repartition_type', 'tax')->count()) {
            throw new Exception('Invoice and credit note repartition should have at least one tax repartition record.');
        }

        foreach ($invoices as $index => $invRep) {
            $refRep = $refunds[$index] ?? null;

            if (! $refRep || $invRep->repartition_type !== $refRep->repartition_type || $invRep->factor_percent !== $refRep->factor_percent) {
                throw new Exception('Invoice and credit note distribution should match (same percentages, in the same order).');
            }
        }

        $positiveFactor = $invoices->where('factor_percent', '>', 0)->sum('factor_percent');
        $negativeFactor = $invoices->where('factor_percent', '<', 0)->sum('factor_percent');

        if (bccomp((string) $positiveFactor, '100', 2) !== 0) {
            throw new Exception('Invoice and credit note distribution should have a total factor (+) equal to 100.');
        }

        if ($negativeFactor && bccomp((string) $negativeFactor, '-100', 2) !== 0) {
            throw new Exception('Invoice and credit note distribution should have a total factor (-) equal to 100.');
        }
    }

    public static function boot()
    {
        parent::boot();

        static::saved(function (self $model) {
            try {
                DB::beginTransaction();

                $invoices = self::where('document_type', 'invoice')
                    ->orderBy('sort')
                    ->get();

                $refunds = self::where('document_type', 'refund')
                    ->orderBy('sort')
                    ->get();

                self::validateRepartitionLines($invoices, $refunds);

                DB::commit();
            } catch (Exception $e) {
                DB::rollBack();

                if ($model->wasRecentlyCreated) {
                    $model->delete();
                }

                throw $e;
            }
        });

        static::deleting(function ($model) {
            try {
                DB::beginTransaction();

                $invoices = self::where('document_type', 'invoice')
                    ->where('id', '!=', $model->id)
                    ->orderBy('sort')
                    ->get();

                $refunds = self::where('document_type', 'refund')
                    ->where('id', '!=', $model->id)
                    ->orderBy('sort')
                    ->get();

                self::validateRepartitionLines($invoices, $refunds);

                DB::commit();
            } catch (Exception $e) {
                DB::rollBack();
                throw $e;
            }
        });
    }
}
