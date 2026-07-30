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
use Webkul\Field\Traits\HasCustomFields;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;
use Webkul\Support\Traits\BelongsToCompanies;

class Account extends Model
{
    use BelongsToCompanies;
    use HasCustomFields;
    use HasFactory;

    protected $table = 'accounts_accounts';

    protected $fillable = [
        'currency_id',
        'creator_id',
        'parent_id',
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

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function descendants(): HasMany
    {
        return $this->children()->with('descendants');
    }

    public function getDescendantIds(): array
    {
        return $this->children
            ->flatMap(fn (self $child) => [$child->id, ...$child->getDescendantIds()])
            ->all();
    }

    public static function incomeTypes(): array
    {
        return [AccountType::INCOME, AccountType::INCOME_OTHER];
    }

    public static function expenseTypes(): array
    {
        return [AccountType::EXPENSE, AccountType::EXPENSE_DEPRECIATION, AccountType::EXPENSE_DIRECT_COST];
    }

    public static function resolveForCompany(?int $accountId, ?int $companyId = null): ?int
    {
        if (! $accountId) {
            return null;
        }

        $companyId = $companyId ?: current_company_id();

        $account = static::withoutGlobalScopes()->find($accountId);

        if (! $account || ! $companyId) {
            return $account?->id;
        }

        $owned = static::withoutGlobalScopes()
            ->whereKey($account->id)
            ->where(owned_by_company($companyId))
            ->exists();

        if ($owned) {
            return $account->id;
        }

        return static::withoutGlobalScopes()
            ->where('code', $account->code)
            ->where('deprecated', false)
            ->where(owned_by_company($companyId))
            ->value('id');
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

    public static function mostUsedForPartner(
        int $companyId,
        int $partnerId,
        string $moveType,
        bool $onlyPreviouslyUsed = false,
        ?int $limit = null
    ): array {
        $since = now()->subYears(2)->toDateString();

        $accountTypes = static::typesForMoveType($moveType);

        $used = static::linesUsedByPartner($companyId, $partnerId, $since, $accountTypes);

        $candidates = $onlyPreviouslyUsed
            ? $used
            : $used->unionAll(static::selectableAccounts($companyId, $since, $accountTypes, $partnerId));

        $ranked = DB::table(DB::raw("({$candidates->toSql()}) as q"))
            ->mergeBindings($candidates)
            ->select('q.account_id')
            ->join('accounts_accounts', 'accounts_accounts.id', '=', 'q.account_id')
            ->groupBy('q.account_id', 'accounts_accounts.code')
            ->orderByRaw('COUNT(q.account_id) DESC')
            ->orderBy('accounts_accounts.code', 'DESC');

        if ($limit) {
            $ranked->limit($limit);
        }

        return $ranked->pluck('account_id')->toArray();
    }

    protected static function typesForMoveType(string $moveType): array
    {
        $move = new Move;

        $asValues = fn (array $types) => array_map(fn (AccountType $type) => $type->value, $types);

        return match (true) {
            in_array($moveType, $move->getInboundTypes(true))  => $asValues(static::incomeTypes()),
            in_array($moveType, $move->getOutboundTypes(true)) => $asValues(static::expenseTypes()),
            default                                            => [],
        };
    }

    protected static function linesUsedByPartner(int $companyId, int $partnerId, string $since, array $accountTypes)
    {
        return DB::table('accounts_account_move_lines')
            ->select('accounts_account_move_lines.account_id')
            ->join('accounts_accounts', 'accounts_accounts.id', '=', 'accounts_account_move_lines.account_id')
            ->where('accounts_account_move_lines.company_id', $companyId)
            ->where('accounts_account_move_lines.partner_id', $partnerId)
            ->where('accounts_accounts.deprecated', false)
            ->whereDate('accounts_account_move_lines.date', '>=', $since)
            ->when($accountTypes, fn ($query) => $query->whereIn('accounts_accounts.account_type', $accountTypes));
    }

    protected static function selectableAccounts(int $companyId, string $since, array $accountTypes, int $partnerId)
    {
        return DB::table('accounts_accounts')
            ->select('accounts_accounts.id as account_id')
            ->leftJoin('accounts_account_move_lines', function ($join) use ($companyId, $partnerId, $since) {
                $join->on('accounts_account_move_lines.account_id', '=', 'accounts_accounts.id')
                    ->where('accounts_account_move_lines.company_id', $companyId)
                    ->where('accounts_account_move_lines.partner_id', $partnerId)
                    ->whereDate('accounts_account_move_lines.date', '>=', $since);
            })
            ->where('accounts_accounts.company_id', $companyId)
            ->where('accounts_accounts.deprecated', false)
            ->when($accountTypes, fn ($query) => $query->whereIn('accounts_accounts.account_type', $accountTypes));
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
