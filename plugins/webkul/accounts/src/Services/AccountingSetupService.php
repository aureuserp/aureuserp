<?php

namespace Webkul\Account\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Webkul\Account\Enums\JournalType;
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
        'bank_account_id',
    ];

    public function isSetUp(Company $company): bool
    {
        return DB::table('accounts_journals')
            ->where('company_id', $company->id)
            ->whereIn('type', [JournalType::SALE->value, JournalType::PURCHASE->value])
            ->distinct()
            ->count('type') >= 2;
    }

    public function setUp(Company $company): bool
    {
        if ($this->isSetUp($company)) {
            return false;
        }

        $templateId = $this->templateCompanyId();

        if ($templateId === null || $templateId === (int) $company->id) {
            return false;
        }

        DB::transaction(function () use ($company, $templateId) {
            $groupMap = $this->copyTaxGroups($company, $templateId);
            $accountMap = $this->copyAccounts($company, $templateId);
            $taxMap = $this->copyTaxes($company, $templateId, $groupMap, $accountMap);
            $journalMap = $this->copyJournals($company, $templateId, $accountMap);
            $this->copySettings($company, $templateId, $accountMap, $taxMap, $journalMap);
        });

        return true;
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
            $overrides = ['company_id' => $company->id];

            foreach (self::JOURNAL_ACCOUNT_COLUMNS as $column) {
                $overrides[$column] = $this->remap($row->{$column} ?? null, $accountMap);
            }

            $map[$row->id] = DB::table('accounts_journals')->insertGetId(
                $this->rowFor($row, $company, $overrides)
            );
        }

        return $map;
    }

    protected function copySettings(Company $company, int $templateId, array $accountMap, array $taxMap, array $journalMap): void
    {
        $rows = DB::table('settings')
            ->whereIn('group', ['accounts_accounts', 'accounts_taxes'])
            ->where('company_id', $templateId)
            ->get();

        foreach ($rows as $row) {
            $value = json_decode($row->payload, true);

            $value = match (true) {
                in_array($row->name, self::ACCOUNT_REF_SETTINGS, true) => $this->remap($value, $accountMap),
                in_array($row->name, self::JOURNAL_REF_SETTINGS, true) => $this->remap($value, $journalMap),
                in_array($row->name, self::TAX_REF_SETTINGS, true)     => $this->remap($value, $taxMap),
                default                                                => $value,
            };

            DB::table('settings')->updateOrInsert(
                ['group' => $row->group, 'name' => $row->name, 'company_id' => $company->id],
                ['payload' => json_encode($value), 'locked' => $row->locked]
            );
        }
    }

    protected function rowFor(object $row, Company $company, array $overrides): array
    {
        $data = (array) $row;

        unset($data['id'], $data['created_at'], $data['updated_at']);

        $data['creator_id'] = Auth::id() ?? $company->creator_id;

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
