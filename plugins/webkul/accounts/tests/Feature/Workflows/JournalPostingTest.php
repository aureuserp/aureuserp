<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Webkul\Account\Contracts\PostingValidator;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\PostingError;
use Webkul\Account\Enums\PostingOperation;
use Webkul\Account\Exceptions\PostingException;
use Webkul\Account\Exceptions\PostingRejectedException;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Account\Models\Move;
use Webkul\Account\Support\Posting\EntryData;
use Webkul\Account\Support\Posting\LineData;
use Webkul\Account\Support\Posting\PostingContext;
use Webkul\Account\Support\Posting\ReversalData;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../Helpers/AccountHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('accounts');

    DB::table('plugins')->updateOrInsert(
        ['name' => 'accounts'],
        ['is_installed' => true, 'is_active' => true, 'updated_at' => now()],
    );

    Package::$plugins = Plugin::all()->keyBy('name');

    URL::resolveMissingNamedRoutesUsing(fn () => '#');

    $this->user = AccountHelper::actingAsAdmin();
    $this->company = AccountHelper::company();
    $this->journal = AccountHelper::generalJournal();
    $this->debitAccount = AccountHelper::account('expense');
    $this->creditAccount = AccountHelper::account('income');
});

function postingEntry(array $overrides = []): EntryData
{
    $test = test();

    return new EntryData(...array_merge([
        'companyId'  => $test->company->id,
        'userId'     => $test->user->id,
        'journalId'  => $test->journal->id,
        'date'       => now()->toDateString(),
        'reference'  => 'Z-REPORT-1',
        'sourceType' => 'pos_session',
        'sourceKey'  => 'session-1',
        'lines'      => [
            new LineData(accountId: $test->debitAccount->id, debit: 100, label: 'Cash'),
            new LineData(accountId: $test->creditAccount->id, credit: 100, label: 'Sales'),
        ],
    ], $overrides));
}

function postingErrorOf(callable $callback): ?PostingError
{
    try {
        $callback();
    } catch (PostingException $exception) {
        return $exception->error;
    }

    return null;
}

function rejectingValidator(PostingOperation $operation): PostingValidator
{
    return new class($operation) implements PostingValidator
    {
        public array $contexts = [];

        public function __construct(protected PostingOperation $operation) {}

        public function validate(PostingContext $context): void
        {
            $this->contexts[] = $context;

            if ($context->operation === $this->operation) {
                throw new PostingRejectedException('closed period');
            }
        }
    };
}

it('creates and posts an entry from structured data', function () {
    $result = AccountFacade::posting()->post(postingEntry());

    $move = $result->move;

    expect($result->replayed)->toBeFalse()
        ->and($move->state)->toBe(MoveState::POSTED)
        ->and($move->name)->not->toBeEmpty()
        ->and($move->journal_id)->toBe($this->journal->id)
        ->and($move->creator_id)->toBe($this->user->id)
        ->and($move->source_document_type)->toBe('pos_session')
        ->and($move->source_document_key)->toBe('session-1')
        ->and($move->lines->pluck('account_id')->sort()->values()->all())->toBe(collect([$this->debitAccount->id, $this->creditAccount->id])->sort()->values()->all())
        ->and((float) $move->lines->sum('debit'))->toBe(100.0)
        ->and((float) $move->lines->sum('credit'))->toBe(100.0);
});

it('returns the original entry when the same source is sent again', function () {
    $first = AccountFacade::posting()->post(postingEntry());

    $second = AccountFacade::posting()->post(postingEntry());

    expect($second->replayed)->toBeTrue()
        ->and($second->move->id)->toBe($first->move->id)
        ->and(Move::where('source_document_key', 'session-1')->count())->toBe(1);
});

it('rejects an unbalanced entry and leaves nothing behind', function () {
    $count = Move::count();

    $error = postingErrorOf(fn () => AccountFacade::posting()->post(postingEntry([
        'lines' => [
            new LineData(accountId: $this->debitAccount->id, debit: 100),
            new LineData(accountId: $this->creditAccount->id, credit: 90),
        ],
    ])));

    expect($error)->toBe(PostingError::UNBALANCED)
        ->and(Move::count())->toBe($count);
});

it('rejects an unknown account and leaves nothing behind', function () {
    $count = Move::count();

    $error = postingErrorOf(fn () => AccountFacade::posting()->post(postingEntry([
        'lines' => [
            new LineData(accountId: $this->debitAccount->id, debit: 100),
            new LineData(accountId: 999999999, credit: 100),
        ],
    ])));

    expect($error)->toBe(PostingError::INVALID_ENTRY)
        ->and(Move::count())->toBe($count);
});

it('rejects a user who is not allowed in the company and restores the acting user', function () {
    $outsider = CompanyHelper::actingAsCompanyUser(CompanyHelper::company());

    $error = postingErrorOf(fn () => AccountFacade::posting()->post(postingEntry(['userId' => $outsider->id])));

    expect($error)->toBe(PostingError::INVALID_ENTRY)
        ->and(Auth::id())->toBe($outsider->id);
});

it('lets a validator reject posting and rolls the entry back', function () {
    AccountFacade::posting()->registerValidator(rejectingValidator(PostingOperation::POST));

    $count = Move::count();

    $error = postingErrorOf(fn () => AccountFacade::posting()->post(postingEntry()));

    expect($error)->toBe(PostingError::REJECTED_BY_VALIDATOR)
        ->and(Move::count())->toBe($count);
});

it('runs validators when an entry is posted outside the service', function () {
    AccountFacade::posting()->registerValidator(rejectingValidator(PostingOperation::POST));

    $entry = AccountHelper::journalEntry($this->journal);
    AccountHelper::entryLine($entry, $this->debitAccount, debit: 100, credit: 0);
    AccountHelper::entryLine($entry, $this->creditAccount, debit: 0, credit: 100);

    expect(postingErrorOf(fn () => AccountHelper::post($entry)))->toBe(PostingError::REJECTED_BY_VALIDATOR)
        ->and($entry->refresh()->state)->toBe(MoveState::DRAFT);
});

it('refuses to post an entry whose persisted lines do not balance', function () {
    $entry = AccountHelper::journalEntry($this->journal);
    AccountHelper::entryLine($entry, $this->debitAccount, debit: 100, credit: 0);
    AccountHelper::entryLine($entry, $this->creditAccount, debit: 0, credit: 90);

    expect(postingErrorOf(fn () => AccountHelper::post($entry)))->toBe(PostingError::UNBALANCED)
        ->and($entry->refresh()->state)->toBe(MoveState::DRAFT);
});

it('reverses an entry so both entries net to zero after recomputation', function () {
    $original = AccountFacade::posting()->post(postingEntry())->move;

    $reversal = AccountFacade::posting()->reverse(new ReversalData(
        companyId: $this->company->id,
        userId: $this->user->id,
        moveId: $original->id,
        date: now()->toDateString(),
        sourceType: 'pos_session',
        sourceKey: 'session-1-reversal',
    ))->move;

    AccountFacade::computeAccountMove($original->refresh());
    AccountFacade::computeAccountMove($reversal->refresh());

    $net = $original->refresh()->lines->merge($reversal->refresh()->lines)
        ->groupBy('account_id')
        ->map(fn ($lines) => round((float) $lines->sum('debit') - (float) $lines->sum('credit'), 2));

    $reversedCash = $reversal->lines->firstWhere('account_id', $this->debitAccount->id);

    expect($reversal->state)->toBe(MoveState::POSTED)
        ->and((float) $reversedCash->debit)->toBe(0.0)
        ->and((float) $reversedCash->credit)->toBe(100.0)
        ->and($reversal->reversed_entry_id)->toBe($original->id)
        ->and($reversal->source_document_key)->toBe('session-1-reversal')
        ->and($net->unique()->values()->all())->toBe([0.0]);
});

it('refuses to reverse an entry twice', function () {
    $original = AccountFacade::posting()->post(postingEntry())->move;

    $reverse = fn (string $key) => AccountFacade::posting()->reverse(new ReversalData(
        companyId: $this->company->id,
        userId: $this->user->id,
        moveId: $original->id,
        sourceType: 'pos_session',
        sourceKey: $key,
    ));

    $reverse('reversal-a');

    expect(postingErrorOf(fn () => $reverse('reversal-b')))->toBe(PostingError::MOVE_NOT_REVERSIBLE);
});

it('passes the reversal date to validators and lets them reject a reversal', function () {
    $original = AccountFacade::posting()->post(postingEntry())->move;

    $validator = rejectingValidator(PostingOperation::REVERSE);

    AccountFacade::posting()->registerValidator($validator);

    $error = postingErrorOf(fn () => AccountFacade::posting()->reverse(new ReversalData(
        companyId: $this->company->id,
        userId: $this->user->id,
        moveId: $original->id,
        date: '2026-01-15',
    )));

    $context = collect($validator->contexts)->last();

    expect($error)->toBe(PostingError::REJECTED_BY_VALIDATOR)
        ->and($context->move->id)->toBe($original->id)
        ->and($context->date->toDateString())->toBe('2026-01-15')
        ->and(Move::where('reversed_entry_id', $original->id)->exists())->toBeFalse();
});
