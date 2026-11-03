<?php

namespace Webkul\Account\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use ReflectionClass;
use ReflectionProperty;
use Webkul\Account\Enums\AccountingSetupResult;
use Webkul\Account\Enums\JournalType;
use Webkul\Account\Exceptions\IncompleteAccountingTemplateException;
use Webkul\Account\Settings\DefaultAccountSettings;
use Webkul\Account\Settings\TaxesSettings;
use Webkul\Support\Models\Company;

class AccountingSetupService
{
    protected const ACCOUNT_REF_SETTINGS = [
        'income_currency_exchange_account_id',
        'expense_currency_exchange_account_id',
        'account_discount_expense_allocation_id',
        'account_discount_income_allocation_id',
        'account_journal_suspense_account_id',
        'transfer_account_id',
        'account_journal_payment_debit_account_id',
        'account_journal_payment_credit_account_id',
        'income_account_id',
        'expense_account_id',
    ];

    protected const JOURNAL_REF_SETTINGS = [
        'currency_exchange_journal_id',
    ];

    protected const TAX_REF_SETTINGS = [
        'account_sale_tax_id',
        'account_purchase_tax_id',
    ];

    protected const JOURNAL_ACCOUNT_COLUMNS = [
        'default_account_id',
        'suspense_account_id',
        'profit_account_id',
        'loss_account_id',
    ];

    protected const SETTINGS_GROUPS = [
        'accounts_accounts',
        'accounts_taxes',
    ];

    protected const REQUIRED_SETTINGS = [
        DefaultAccountSettings::class,
        TaxesSettings::class,
    ];

    protected const REQUIRED_JOURNAL_TYPES = [
        JournalType::SALE,
        JournalType::PURCHASE,
    ];

    public function isSetUp(Company $company): bool
    {
        return DB::table('accounts_journals')
            ->where('company_id', $company->id)
            ->whereIn('type', [JournalType::SALE->value, JournalType::PURCHASE->value])
            ->distinct()
            ->count('type') >= 2;
    }

    public function setUp(Company $company): AccountingSetupResult
    {
        if ($this->isSetUp($company)) {
            return AccountingSetupResult::ALREADY_SET_UP;
        }

        $templateId = $this->templateCompanyId();

        if ($templateId === null || $templateId === (int) $company->id) {
            Log::warning('Skipping accounting setup: no usable template company.', [
                'company_id'  => (int) $company->id,
                'template_id' => $templateId,
            ]);

            return AccountingSetupResult::NO_TEMPLATE;
        }

        try {
            return DB::transaction(function () use ($company, $templateId): AccountingSetupResult {
                Company::withoutGlobalScopes()->whereKey($company->getKey())->lockForUpdate()->first();

                if ($this->isSetUp($company)) {
                    return AccountingSetupResult::ALREADY_SET_UP;
                }

                $groupMap = $this->copyTaxGroups($company, $templateId);
                $accountMap = $this->copyAccounts($company, $templateId);
                $taxMap = $this->copyTaxes($company, $templateId, $groupMap, $accountMap);
                $this->copyTaxPartitionLines($company, $taxMap, $accountMap);
                $this->copyTaxChildren($taxMap);
                $this->copyAccountTaxes($accountMap, $taxMap);
                $journalMap = $this->copyJournals($company, $templateId, $accountMap);
                $this->copySettings($company, $templateId, $accountMap, $taxMap, $journalMap);

                $missing = $this->missingRequirements($company, $templateId);

                if ($missing !== []) {
                    throw new IncompleteAccountingTemplateException($missing);
                }

                return AccountingSetupResult::COMPLETED;
            });
        } catch (IncompleteAccountingTemplateException $exception) {
            Log::warning('Rolled back accounting setup: the template company is incomplete.', [
                'company_id'  => (int) $company->id,
                'template_id' => $templateId,
                'missing'     => $exception->missing,
            ]);

            return AccountingSetupResult::INCOMPLETE;
        }
    }

    protected function missingRequirements(Company $company, int $templateId): array
    {
        $journalTypes = DB::table('accounts_journals')
            ->where('company_id', $company->id)
            ->distinct()
            ->pluck('type')
            ->all();

        $missing = [];

        foreach (self::REQUIRED_JOURNAL_TYPES as $journalType) {
            if (! in_array($journalType->value, $journalTypes, true)) {
                $missing[] = 'journal.'.$journalType->value;
            }
        }

        foreach (self::REQUIRED_SETTINGS as $settingsClass) {
            $group = $settingsClass::group();

            $values = $this->resolvedSettingValues($group, [$templateId, (int) $company->id]);

            foreach ($this->requiredSettingNames($settingsClass) as $name) {
                if (blank($values[$name] ?? null)) {
                    $missing[] = $group.'.'.$name;
                }
            }
        }

        foreach (self::SETTINGS_GROUPS as $group) {
            foreach ($this->resolvedSettingValues($group, [$templateId, (int) $company->id]) as $name => $value) {
                if (filled($value) && ! $this->settingReferenceBelongsTo($company, $name, $value)) {
                    $missing[] = $group.'.'.$name;
                }
            }
        }

        return $missing;
    }

    protected function copyTaxGroups(Company $company, int $templateId): array
    {
        $map = [];

        foreach (DB::table('accounts_tax_groups')->where('company_id', $templateId)->get() as $row) {
            $map[$row->id] = DB::table('accounts_tax_groups')->insertGetId(
                $this->rowFor($row, $company, ['company_id' => $company->id])
            );
        }

        return $map;
    }

    protected function copyAccounts(Company $company, int $templateId): array
    {
        $accountIds = DB::table('accounts_account_companies')
            ->where('company_id', $templateId)
            ->pluck('account_id');

        $rows = DB::table('accounts_accounts')->whereIn('id', $accountIds)->get();

        $map = [];

        foreach ($rows as $row) {
            $newId = DB::table('accounts_accounts')->insertGetId(
                $this->rowFor($row, $company, ['parent_id' => null])
            );

            $map[$row->id] = $newId;

            DB::table('accounts_account_companies')->insert([
                'account_id' => $newId,
                'company_id' => $company->id,
            ]);
        }

        foreach ($rows as $row) {
            if ($row->parent_id && isset($map[$row->parent_id])) {
                DB::table('accounts_accounts')
                    ->where('id', $map[$row->id])
                    ->update(['parent_id' => $map[$row->parent_id]]);
            }
        }

        return $map;
    }

    protected function copyTaxes(Company $company, int $templateId, array $groupMap, array $accountMap): array
    {
        $map = [];

        foreach (DB::table('accounts_taxes')->where('company_id', $templateId)->get() as $row) {
            $map[$row->id] = DB::table('accounts_taxes')->insertGetId(
                $this->rowFor($row, $company, [
                    'company_id'                       => $company->id,
                    'tax_group_id'                     => $groupMap[$row->tax_group_id] ?? $row->tax_group_id,
                    'cash_basis_transition_account_id' => $this->remap($row->cash_basis_transition_account_id, $accountMap),
                ])
            );
        }

        return $map;
    }

    protected function copyJournals(Company $company, int $templateId, array $accountMap): array
    {
        $map = [];

        foreach (DB::table('accounts_journals')->where('company_id', $templateId)->get() as $row) {
            $overrides = [
                'company_id'      => $company->id,
                'bank_account_id' => null,
            ];

            foreach (self::JOURNAL_ACCOUNT_COLUMNS as $column) {
                $overrides[$column] = $this->remap($row->{$column} ?? null, $accountMap);
            }

            $map[$row->id] = DB::table('accounts_journals')->insertGetId(
                $this->rowFor($row, $company, $overrides)
            );
        }

        return $map;
    }

    protected function copyTaxPartitionLines(Company $company, array $taxMap, array $accountMap): void
    {
        $rows = DB::table('accounts_tax_partition_lines')
            ->whereIn('tax_id', array_keys($taxMap))
            ->orderBy('id')
            ->get();

        foreach ($rows as $row) {
            DB::table('accounts_tax_partition_lines')->insert(
                $this->rowFor($row, $company, [
                    'tax_id'     => $taxMap[$row->tax_id],
                    'account_id' => $this->remap($row->account_id, $accountMap),
                    'company_id' => $company->id,
                ])
            );
        }
    }

    protected function copyTaxChildren(array $taxMap): void
    {
        $rows = DB::table('accounts_tax_taxes')
            ->whereIn('parent_tax_id', array_keys($taxMap))
            ->get();

        foreach ($rows as $row) {
            DB::table('accounts_tax_taxes')->insert([
                'parent_tax_id' => $taxMap[$row->parent_tax_id],
                'child_tax_id'  => $this->remap($row->child_tax_id, $taxMap),
            ]);
        }
    }

    protected function copyAccountTaxes(array $accountMap, array $taxMap): void
    {
        $rows = DB::table('accounts_account_taxes')
            ->whereIn('account_id', array_keys($accountMap))
            ->whereIn('tax_id', array_keys($taxMap))
            ->get();

        foreach ($rows as $row) {
            DB::table('accounts_account_taxes')->insert([
                'account_id' => $accountMap[$row->account_id],
                'tax_id'     => $taxMap[$row->tax_id],
            ]);
        }
    }

    protected function copySettings(Company $company, int $templateId, array $accountMap, array $taxMap, array $journalMap): void
    {
        foreach (self::SETTINGS_GROUPS as $group) {
            foreach ($this->resolvedSettingRows($group, [$templateId]) as $name => $row) {
                $value = json_decode((string) $row->payload, true);

                $value = match (true) {
                    in_array($name, self::ACCOUNT_REF_SETTINGS, true) => $this->remap($value, $accountMap),
                    in_array($name, self::JOURNAL_REF_SETTINGS, true) => $this->remap($value, $journalMap),
                    in_array($name, self::TAX_REF_SETTINGS, true)     => $this->remap($value, $taxMap),
                    default                                           => $value,
                };

                DB::table('settings')->updateOrInsert(
                    ['group' => $group, 'name' => $name, 'company_id' => $company->id],
                    ['payload' => json_encode($value), 'locked' => $row->locked]
                );
            }
        }
    }

    protected function settingReferenceBelongsTo(Company $company, string $name, mixed $value): bool
    {
        return match (true) {
            in_array($name, self::ACCOUNT_REF_SETTINGS, true) => DB::table('accounts_account_companies')
                ->where('account_id', $value)
                ->where('company_id', $company->id)
                ->exists(),
            in_array($name, self::JOURNAL_REF_SETTINGS, true) => DB::table('accounts_journals')
                ->where('id', $value)
                ->where('company_id', $company->id)
                ->exists(),
            in_array($name, self::TAX_REF_SETTINGS, true) => DB::table('accounts_taxes')
                ->where('id', $value)
                ->where('company_id', $company->id)
                ->exists(),
            default => true,
        };
    }

    protected function resolvedSettingRows(string $group, array $companyIds): array
    {
        $priorities = array_flip(array_map('intval', $companyIds));

        return DB::table('settings')
            ->where('group', $group)
            ->where(fn ($query) => $query->whereNull('company_id')->orWhereIn('company_id', $companyIds))
            ->get(['name', 'company_id', 'payload', 'locked'])
            ->sortBy(fn (object $row): int => $row->company_id === null ? 0 : $priorities[(int) $row->company_id] + 1)
            ->keyBy('name')
            ->all();
    }

    protected function resolvedSettingValues(string $group, array $companyIds): array
    {
        return collect($this->resolvedSettingRows($group, $companyIds))
            ->map(fn (object $row): mixed => json_decode((string) $row->payload, true))
            ->all();
    }

    protected function requiredSettingNames(string $settingsClass): array
    {
        return collect((new ReflectionClass($settingsClass))->getProperties(ReflectionProperty::IS_PUBLIC))
            ->filter(fn (ReflectionProperty $property): bool => ! $property->isStatic()
                && $property->getDeclaringClass()->getName() === $settingsClass
                && $property->getType() !== null
                && ! $property->getType()->allowsNull())
            ->map(fn (ReflectionProperty $property): string => $property->getName())
            ->values()
            ->all();
    }

    protected function rowFor(object $row, Company $company, array $overrides): array
    {
        $data = (array) $row;

        unset($data['id'], $data['created_at'], $data['updated_at']);

        $data['creator_id'] = Auth::id() ?? $company->creator_id;

        if (array_key_exists('currency_id', $data) && $data['currency_id'] !== null) {
            $data['currency_id'] = $company->currency_id ?? $data['currency_id'];
        }

        $data = array_merge($data, $overrides);

        $data['created_at'] = now();
        $data['updated_at'] = now();

        return $data;
    }

    protected function remap($value, array $map)
    {
        if ($value === null) {
            return null;
        }

        return $map[$value] ?? $value;
    }

    protected function templateCompanyId(): ?int
    {
        $payload = DB::table('settings')
            ->where('group', 'general')
            ->where('name', 'default_company_id')
            ->value('payload');

        $decoded = $payload !== null ? json_decode($payload, true) : null;

        if (is_int($decoded) && $decoded > 0) {
            return $decoded;
        }

        return DB::table('companies')->min('id');
    }
}
