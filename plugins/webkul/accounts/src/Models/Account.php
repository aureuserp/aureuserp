<?php

namespace Webkul\Account\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Webkul\Account\Database\Factories\AccountFactory;
use Webkul\Account\Enums\AccountType;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;

class Account extends Model
{
    use HasFactory;

    protected $table = 'accounts_accounts';

    protected $fillable = [
        'currency_id',
        'creator_id',
        'account_type',
        'name',
        'code',
        'note',
        'deprecated',
        'reconcile',
        'non_trade',
    ];

    protected $casts = [
        'deprecated'   => 'boolean',
        'reconcile'    => 'boolean',
        'non_trade'    => 'boolean',
        'account_type' => AccountType::class,
    ];

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function taxes(): BelongsToMany
    {
        return $this->belongsToMany(Tax::class, 'accounts_account_taxes', 'account_id', 'tax_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'accounts_account_account_tags', 'account_id', 'account_tag_id');
    }

    public function journals(): BelongsToMany
    {
        return $this->belongsToMany(Journal::class, 'accounts_account_journals', 'account_id', 'journal_id');
    }

    public function moveLines(): HasMany
    {
        return $this->hasMany(MoveLine::class, 'account_id');
    }

    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class, 'accounts_account_companies', 'account_id', 'company_id');
    }

    public static function getMostFrequentAccountsForPartner(
        int $companyId,
        int $partnerId,
        string $moveType,
        bool $filterNeverUsedAccounts = false,
        ?int $limit = null
    ) {
        $minDate = now()->subYears(2)->toDateString();

        $group = null;

        if (in_array($moveType, (new Move)->getInboundTypes(true))) {
            $group = 'income';
        } elseif (in_array($moveType, (new Move)->getOutboundTypes(true))) {
            $group = 'expense';
        }

        $query = DB::table('accounts_account_move_lines')
            ->select('accounts_account_move_lines.account_id')
            ->join('accounts_accounts', 'accounts_accounts.id', '=', 'accounts_account_move_lines.account_id')
            ->where('accounts_account_move_lines.company_id', $companyId)
            ->where('accounts_account_move_lines.partner_id', $partnerId)
            ->where('accounts_accounts.deprecated', false)
            ->whereDate('accounts_account_move_lines.date', '>=', $minDate);

        if ($group) {
            $query->where('accounts_accounts.internal_group', $group);
        }

        if (! $filterNeverUsedAccounts) {
            $accountsBase = DB::table('accounts_accounts')
                ->select('accounts_accounts.id as account_id')
                ->leftJoin('accounts_account_move_lines', function ($j) use ($companyId, $partnerId, $minDate) {
                    $j->on('accounts_account_move_lines.account_id', '=', 'accounts_accounts.id')
                        ->where('accounts_account_move_lines.company_id', $companyId)
                        ->where('accounts_account_move_lines.partner_id', $partnerId)
                        ->whereDate('accounts_account_move_lines.date', '>=', $minDate);
                })
                ->where('accounts_accounts.company_id', $companyId)
                ->where('accounts_accounts.deprecated', false);

            if ($group) {
                $accountsBase->where('accounts_accounts.internal_group', $group);
            }

            $query = $query->unionAll($accountsBase);
        }

        $query = DB::table(DB::raw("({$query->toSql()}) as q"))
            ->mergeBindings($query)
            ->select('q.account_id')
            ->join('accounts_accounts', 'accounts_accounts.id', '=', 'q.account_id')
            ->groupBy('q.account_id')
            ->orderByRaw('COUNT(q.account_id) DESC')
            ->orderBy('accounts_accounts.code', 'DESC');

        if ($limit) {
            $query->limit($limit);
        }

        return $query->pluck('account_id')->toArray();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($account) {
            $account->creator_id ??= Auth::id();
        });
    }

    protected static function newFactory()
    {
        return AccountFactory::new();
    }
}
