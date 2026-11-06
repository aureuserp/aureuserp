<?php

namespace Webkul\Account\Services;

use Carbon\CarbonImmutable;
use Closure;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;
use Webkul\Account\Contracts\PostingValidator;
use Webkul\Account\Enums\DisplayType;
use Webkul\Account\Enums\JournalType;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Enums\PostingError;
use Webkul\Account\Exceptions\MissingJournalException;
use Webkul\Account\Exceptions\PostingException;
use Webkul\Account\Models\Account;
use Webkul\Account\Models\Journal;
use Webkul\Account\Models\Move;
use Webkul\Account\Models\MoveLine;
use Webkul\Account\Models\Tax;
use Webkul\Account\Support\Posting\EntryData;
use Webkul\Account\Support\Posting\LineData;
use Webkul\Account\Support\Posting\PostingResult;
use Webkul\Account\Support\Posting\ReversalData;
use Webkul\Partner\Models\Partner;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;
use Webkul\Support\Services\CompanyContext;

class JournalPoster
{
    public function __construct(
        protected MoveWorkflow $workflow,
        protected PostingValidatorRegistry $validators,
    ) {}

    public function registerValidator(PostingValidator|string $validator): void
    {
        $this->validators->register($validator);
    }

    public function post(EntryData $data): PostingResult
    {
        return $this->actingAs($data->companyId, $data->userId, fn (): PostingResult => $this->once(
            $data->companyId,
            $data->sourceType,
            $data->sourceKey,
            fn (): Move => $this->guard(fn (): Move => DB::transaction(fn (): Move => $this->createAndPost($data))),
            fn (Move $existing): bool => $this->isSameEntry($existing, $data),
        ));
    }

    public function reverse(ReversalData $data): PostingResult
    {
        return $this->actingAs($data->companyId, $data->userId, fn (): PostingResult => $this->once(
            $data->companyId,
            $data->sourceType,
            $data->sourceKey,
            fn (): Move => $this->guard(fn (): Move => DB::transaction(fn (): Move => $this->reverseAndPost($data))),
            fn (Move $existing): bool => (int) $existing->reversed_entry_id === $data->moveId
                && ($data->date === null || $existing->date?->toDateString() === $this->date($data->date)),
        ));
    }

    protected function createAndPost(EntryData $data): Move
    {
        $company = Company::find($data->companyId);

        $currency = $data->currencyId ? Currency::find($data->currencyId) : $company->currency;

        $this->assertEntry($data, $company, $currency);

        $move = Move::create([
            'move_type'            => MoveType::ENTRY,
            'state'                => MoveState::DRAFT,
            'company_id'           => $company->id,
            'journal_id'           => $data->journalId,
            'currency_id'          => $currency->id,
            'date'                 => $this->date($data->date),
            'reference'            => $data->reference,
            'narration'            => $data->narration,
            'source_document_type' => $data->sourceType,
            'source_document_key'  => $data->sourceKey,
        ]);

        foreach ($data->lines as $line) {
            [$debit, $credit, $amountCurrency] = $this->storedAmounts($line, $company, $currency);

            $moveLine = MoveLine::create([
                'move_id'         => $move->id,
                'display_type'    => DisplayType::PRODUCT,
                'account_id'      => $line->accountId,
                'partner_id'      => $line->partnerId,
                'name'            => $line->label,
                'debit'           => $debit,
                'credit'          => $credit,
                'balance'         => $debit - $credit,
                'amount_currency' => $amountCurrency,
                'currency_id'     => $currency->id,
                'tax_line_id'     => $line->taxLineId,
            ]);

            if ($line->taxIds) {
                $moveLine->taxes()->sync($line->taxIds);
            }
        }

        return $this->workflow->post($move->refresh())->refresh();
    }

    protected function reverseAndPost(ReversalData $data): Move
    {
        $move = Move::withoutGlobalScopes()
            ->where('company_id', $data->companyId)
            ->lockForUpdate()
            ->find($data->moveId);

        if (! $move) {
            throw new PostingException(PostingError::INVALID_ENTRY, ['field' => 'move_id']);
        }

        $isReversed = Move::withoutGlobalScopes()
            ->where('reversed_entry_id', $move->id)
            ->where('state', '!=', MoveState::CANCEL)
            ->exists();

        if ($move->state !== MoveState::POSTED || $isReversed) {
            throw new PostingException(PostingError::MOVE_NOT_REVERSIBLE, ['move' => $move->name]);
        }

        return $this->workflow->reverse(collect([$move]), [[
            'date'                 => $this->date($data->date),
            'reference'            => $data->reference ?? $move->reference,
            'source_document_type' => $data->sourceType,
            'source_document_key'  => $data->sourceKey,
        ]], cancel: true)->first()->refresh();
    }

    protected function storedAmounts(LineData $line, Company $company, Currency $currency): array
    {
        $debit = $company->currency->round($line->debit);

        $credit = $company->currency->round($line->credit);

        if ($currency->id === $company->currency_id) {
            return [$debit, $credit, $debit - $credit];
        }

        return [$debit, $credit, $currency->round((float) $line->amountCurrency)];
    }

    protected function guard(Closure $callback): Move
    {
        try {
            return $callback();
        } catch (PostingException|UniqueConstraintViolationException $exception) {
            throw $exception;
        } catch (MissingJournalException $exception) {
            throw new PostingException(PostingError::INVALID_ENTRY, ['field' => 'journal_id'], $exception->getMessage(), $exception);
        } catch (Throwable $exception) {
            $this->unexpected($exception);
        }
    }

    protected function unexpected(Throwable $exception): never
    {
        Log::error('Journal entry posting failed.', ['exception' => $exception]);

        throw new PostingException(PostingError::MOVE_NOT_POSTABLE, ['reason' => __('accounts::system.posting.unexpected-error')], previous: $exception);
    }

    protected function assertEntry(EntryData $data, Company $company, ?Currency $currency): void
    {
        if (! $currency) {
            $this->invalid('currency_id');
        }

        if (count($data->lines) < 2) {
            $this->invalid('lines');
        }

        $companyCurrency = $company->currency;

        $isForeign = $currency->id !== $company->currency_id;

        $debit = 0.0;

        $credit = 0.0;

        $foreignDebit = 0.0;

        $foreignCredit = 0.0;

        foreach ($data->lines as $index => $line) {
            if (! $line instanceof LineData || ($isForeign && $line->amountCurrency === null)) {
                $this->invalid("lines.{$index}");
            }

            [$lineDebit, $lineCredit, $amountCurrency] = $this->storedAmounts($line, $company, $currency);

            $hasDebit = ! $companyCurrency->isZero($lineDebit);

            $hasCredit = ! $companyCurrency->isZero($lineCredit);

            if (
                $companyCurrency->compareAmounts($lineDebit, 0) < 0
                || $companyCurrency->compareAmounts($lineCredit, 0) < 0
                || ($hasDebit && $hasCredit)
                || (! $hasDebit && ! $hasCredit && $currency->isZero($amountCurrency))
                || ($isForeign && $hasDebit && $currency->compareAmounts($amountCurrency, 0) < 0)
                || ($isForeign && $hasCredit && $currency->compareAmounts($amountCurrency, 0) > 0)
            ) {
                $this->invalid("lines.{$index}");
            }

            $debit += $lineDebit;

            $credit += $lineCredit;

            if ($currency->compareAmounts($amountCurrency, 0) > 0) {
                $foreignDebit += $amountCurrency;
            } else {
                $foreignCredit -= $amountCurrency;
            }
        }

        if ($companyCurrency->compareAmounts($debit, $credit) !== 0) {
            $this->unbalanced($data, $companyCurrency, $debit, $credit);
        }

        if ($isForeign && $currency->compareAmounts($foreignDebit, $foreignCredit) !== 0) {
            $this->unbalanced($data, $currency, $foreignDebit, $foreignCredit);
        }

        $lines = collect($data->lines);

        $accountIds = $lines->pluck('accountId')->unique();

        $accounts = Account::withoutGlobalScopes()
            ->whereKey($accountIds)
            ->where(fn ($query) => $query
                ->whereHas('companies', fn ($companies) => $companies->where('companies.id', $company->id))
                ->orWhereDoesntHave('companies'))
            ->count();

        if ($accounts !== $accountIds->count()) {
            $this->invalid('account_id');
        }

        $partnerIds = $lines->pluck('partnerId')->filter()->unique();

        $partners = Partner::withoutGlobalScopes()
            ->whereKey($partnerIds)
            ->where(fn ($query) => $query->whereNull('company_id')->orWhere('company_id', $company->id))
            ->count();

        if ($partners !== $partnerIds->count()) {
            $this->invalid('partner_id');
        }

        $taxIds = $lines->pluck('taxIds')->flatten()->merge($lines->pluck('taxLineId'))->filter()->unique();

        if (Tax::withoutGlobalScopes()->whereKey($taxIds)->where('company_id', $company->id)->count() !== $taxIds->count()) {
            $this->invalid('tax_ids');
        }

        if ($data->journalId && ! Journal::withoutGlobalScopes()
            ->whereKey($data->journalId)
            ->where('company_id', $company->id)
            ->where('type', JournalType::GENERAL)
            ->exists()) {
            $this->invalid('journal_id');
        }
    }

    protected function once(int $companyId, ?string $sourceType, ?string $sourceKey, Closure $callback, Closure $isSameRequest): PostingResult
    {
        if (($sourceType === null) !== ($sourceKey === null) || mb_strlen((string) $sourceType) > 100 || mb_strlen((string) $sourceKey) > 191) {
            $this->invalid('source');
        }

        if ($existing = $this->findBySource($companyId, $sourceType, $sourceKey)) {
            return $this->replay($existing, $isSameRequest);
        }

        try {
            return new PostingResult($callback());
        } catch (UniqueConstraintViolationException $exception) {
            if ($existing = $this->findBySource($companyId, $sourceType, $sourceKey)) {
                return $this->replay($existing, $isSameRequest);
            }

            $this->unexpected($exception);
        }
    }

    protected function replay(Move $existing, Closure $isSameRequest): PostingResult
    {
        if (! $isSameRequest($existing)) {
            throw new PostingException(PostingError::DUPLICATE_SOURCE, ['move' => $existing->name]);
        }

        return new PostingResult($existing, replayed: true);
    }

    protected function isSameEntry(Move $existing, EntryData $data): bool
    {
        $company = Company::withoutGlobalScopes()->find($data->companyId);

        $currency = $data->currencyId ? Currency::find($data->currencyId) : $company->currency;

        if (
            ! $currency
            || collect($data->lines)->contains(fn ($line): bool => ! $line instanceof LineData)
            || (int) $existing->currency_id !== $currency->id
            || ($data->journalId !== null && (int) $existing->journal_id !== $data->journalId)
            || ($data->date !== null && $existing->date?->toDateString() !== $this->date($data->date))
        ) {
            return false;
        }

        $amount = fn (float $value): string => number_format($value + 0.0, 4, '.', '');

        $signature = fn ($lines): array => collect($lines)->map(fn (array $line): string => implode('|', $line))->sort()->values()->all();

        $stored = MoveLine::withoutGlobalScopes()
            ->where('move_id', $existing->id)
            ->get()
            ->map(fn (MoveLine $line): array => [
                $line->account_id,
                $line->partner_id,
                $line->tax_line_id,
                $amount($company->currency->round((float) $line->debit)),
                $amount($company->currency->round((float) $line->credit)),
                $amount($currency->round((float) $line->amount_currency)),
            ]);

        $requested = collect($data->lines)->map(function (LineData $line) use ($company, $currency, $amount): array {
            [$debit, $credit, $amountCurrency] = $this->storedAmounts($line, $company, $currency);

            return [
                $line->accountId,
                $line->partnerId,
                $line->taxLineId,
                $amount($debit),
                $amount($credit),
                $amount($amountCurrency),
            ];
        });

        return $signature($stored) === $signature($requested);
    }

    protected function findBySource(int $companyId, ?string $sourceType, ?string $sourceKey): ?Move
    {
        if ($sourceType === null) {
            return null;
        }

        return Move::withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->where('source_document_type', $sourceType)
            ->where('source_document_key', $sourceKey)
            ->first();
    }

    protected function actingAs(int $companyId, int $userId, Closure $callback): mixed
    {
        $user = User::find($userId);

        if (! $user) {
            $this->invalid('user_id');
        }

        $previousUser = Auth::user();

        $previousCompanies = session(CompanyContext::SESSION_KEY);

        Auth::setUser($user);

        app()->forgetInstance(CompanyContext::class);

        try {
            $context = app(CompanyContext::class);

            if (! Company::whereKey($companyId)->exists() || (! $context->seesAllCompanies() && ! in_array($companyId, $context->allowedIds()))) {
                $this->invalid('company_id');
            }

            session([CompanyContext::SESSION_KEY => [$companyId]]);

            app()->forgetInstance(CompanyContext::class);

            return $callback();
        } finally {
            $previousUser ? Auth::setUser($previousUser) : Auth::forgetUser();

            session([CompanyContext::SESSION_KEY => $previousCompanies]);

            app()->forgetInstance(CompanyContext::class);
        }
    }

    protected function date($date): string
    {
        try {
            return CarbonImmutable::parse($date ?? now())->toDateString();
        } catch (Throwable) {
            $this->invalid('date');
        }
    }

    protected function unbalanced(EntryData $data, Currency $currency, float $debit, float $credit): never
    {
        throw new PostingException(PostingError::UNBALANCED, [
            'move'   => $data->reference ?? $data->sourceKey ?? '',
            'debit'  => $currency->round($debit),
            'credit' => $currency->round($credit),
        ]);
    }

    protected function invalid(string $field): never
    {
        throw new PostingException(PostingError::INVALID_ENTRY, ['field' => $field]);
    }
}
