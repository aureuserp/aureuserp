<?php

use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Webkul\Account\Contracts\PostingValidator;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Enums\PostingError;
use Webkul\Account\Enums\PostingOperation;
use Webkul\Account\Exceptions\MissingJournalException;
use Webkul\Account\Exceptions\PostingException;
use Webkul\Account\Exceptions\PostingRejectedException;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Account\Models\Move;
use Webkul\Account\Services\ExchangeDifferenceRecorder;
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
        'reference'  => 'BATCH-REF-1',
        'sourceType' => 'external_batch',
        'sourceKey'  => 'batch-1',
        'lines'      => [
            new LineData(accountId: $test->debitAccount->id, debit: 100, label: 'Cash'),
            new LineData(accountId: $test->creditAccount->id, credit: 100, label: 'Sales'),
        ],
    ], $overrides));
}

function postingExceptionOf(callable $callback): ?PostingException
{
    try {
        $callback();
    } catch (PostingException $exception) {
        return $exception;
    }

    return null;
}

function postingErrorOf(callable $callback): ?PostingError
{
    return postingExceptionOf($callback)?->error;
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
        ->and($move->source_document_type)->toBe('external_batch')
        ->and($move->source_document_key)->toBe('batch-1')
        ->and($move->lines->pluck('account_id')->sort()->values()->all())->toBe(collect([$this->debitAccount->id, $this->creditAccount->id])->sort()->values()->all())
        ->and((float) $move->lines->sum('debit'))->toBe(100.0)
        ->and((float) $move->lines->sum('credit'))->toBe(100.0);
});

it('returns the original entry when the same source is sent again', function () {
    $first = AccountFacade::posting()->post(postingEntry());

    $second = AccountFacade::posting()->post(postingEntry());

    expect($second->replayed)->toBeTrue()
        ->and($second->move->id)->toBe($first->move->id)
        ->and(Move::where('source_document_key', 'batch-1')->count())->toBe(1);
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
        sourceType: 'external_batch',
        sourceKey: 'batch-1-reversal',
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
        ->and($reversal->source_document_key)->toBe('batch-1-reversal')
        ->and($net->unique()->values()->all())->toBe([0.0]);
});

it('refuses to reverse an entry twice', function () {
    $original = AccountFacade::posting()->post(postingEntry())->move;

    $reverse = fn (string $key) => AccountFacade::posting()->reverse(new ReversalData(
        companyId: $this->company->id,
        userId: $this->user->id,
        moveId: $original->id,
        sourceType: 'external_batch',
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

it('rejects the same source key when the lines differ', function () {
    AccountFacade::posting()->post(postingEntry());

    $error = postingErrorOf(fn () => AccountFacade::posting()->post(postingEntry([
        'lines' => [
            new LineData(accountId: $this->debitAccount->id, debit: 777),
            new LineData(accountId: $this->creditAccount->id, credit: 777),
        ],
    ])));

    expect($error)->toBe(PostingError::DUPLICATE_SOURCE)
        ->and(Move::where('source_document_key', 'batch-1')->count())->toBe(1);
});

it('stores line amounts at currency precision so a sub-cent residue never reaches the ledger', function () {
    $move = AccountFacade::posting()->post(postingEntry([
        'lines' => [
            new LineData(accountId: $this->debitAccount->id, debit: 100),
            new LineData(accountId: $this->creditAccount->id, credit: 100.004),
        ],
    ]))->move;

    expect((float) $move->lines->sum('debit'))->toBe(100.0)
        ->and((float) $move->lines->sum('credit'))->toBe(100.0);
});

it('refuses to post persisted lines that differ by a residue under half a cent', function () {
    $entry = AccountHelper::journalEntry($this->journal);
    AccountHelper::entryLine($entry, $this->debitAccount, debit: 100, credit: 0);
    AccountHelper::entryLine($entry, $this->creditAccount, debit: 0, credit: 100.004);

    $exception = postingExceptionOf(fn () => AccountHelper::post($entry));

    expect($exception?->error)->toBe(PostingError::UNBALANCED)
        ->and($exception->getMessage())->toBe(__('accounts::system.posting.unbalanced', $exception->details))
        ->and($exception->details['debit'])->toEqualWithDelta(100.0, 0.00001)
        ->and($exception->details['credit'])->toEqualWithDelta(100.004, 0.00001)
        ->and($entry->refresh()->state)->toBe(MoveState::DRAFT)
        ->and($entry->lines()->where('parent_state', MoveState::POSTED)->exists())->toBeFalse();
});

it('runs validators on exchange difference entries and leaves nothing behind when rejected', function () {
    AccountFacade::posting()->registerValidator(rejectingValidator(PostingOperation::POST));

    $count = Move::count();

    $differences = [[
        'move_values' => [
            'move_type'  => MoveType::ENTRY,
            'date'       => now()->toDateString(),
            'journal_id' => $this->journal->id,
            'lines'      => [
                ['name' => 'Exchange', 'debit' => 5, 'credit' => 0, 'amount_currency' => 0, 'account_id' => $this->debitAccount->id, 'currency_id' => $this->company->currency_id],
                ['name' => 'Exchange', 'debit' => 0, 'credit' => 5, 'amount_currency' => 0, 'account_id' => $this->creditAccount->id, 'currency_id' => $this->company->currency_id],
            ],
        ],
        'to_reconcile' => [],
    ]];

    expect(postingErrorOf(fn () => app(ExchangeDifferenceRecorder::class)->postDifferences($differences)))->toBe(PostingError::REJECTED_BY_VALIDATOR)
        ->and(Move::count())->toBe($count);
});

it('checks balance on the amounts that will be stored, before anything is written', function () {
    $count = Move::count();

    $error = postingErrorOf(fn () => AccountFacade::posting()->post(postingEntry([
        'lines' => [
            new LineData(accountId: $this->debitAccount->id, debit: 10.005),
            new LineData(accountId: $this->creditAccount->id, credit: 5.0025),
            new LineData(accountId: $this->creditAccount->id, credit: 5.0025),
        ],
    ])));

    expect($error)->toBe(PostingError::UNBALANCED)
        ->and(Move::count())->toBe($count);
});

it('authorizes the acting user before looking up an existing source key', function () {
    AccountFacade::posting()->post(postingEntry());

    $outsider = CompanyHelper::actingAsCompanyUser(CompanyHelper::company());

    $exception = postingExceptionOf(fn () => AccountFacade::posting()->post(postingEntry(['userId' => $outsider->id])));

    expect($exception?->error)->toBe(PostingError::INVALID_ENTRY)
        ->and($exception->details)->toBe(['field' => 'company_id']);
});

it('authorizes the acting user before looking up an existing reversal source key', function () {
    $original = AccountFacade::posting()->post(postingEntry())->move;

    $reversal = fn (int $userId) => AccountFacade::posting()->reverse(new ReversalData(
        companyId: $this->company->id,
        userId: $userId,
        moveId: $original->id,
        sourceType: 'external_batch',
        sourceKey: 'batch-1-reversal',
    ));

    $reversal($this->user->id);

    $outsider = CompanyHelper::actingAsCompanyUser(CompanyHelper::company());

    expect(postingErrorOf(fn () => $reversal($outsider->id)))->toBe(PostingError::INVALID_ENTRY);
});

it('replays a request whose header and lines match the stored entry', function () {
    $partner = AccountHelper::partner();

    $entry = fn () => postingEntry([
        'date'  => '2026-03-10',
        'lines' => [
            new LineData(accountId: $this->debitAccount->id, debit: 100, partnerId: $partner->id),
            new LineData(accountId: $this->creditAccount->id, credit: 100),
        ],
    ]);

    $first = AccountFacade::posting()->post($entry());

    $second = AccountFacade::posting()->post($entry());

    expect($second->replayed)->toBeTrue()
        ->and($second->move->id)->toBe($first->move->id);
});

it('rejects the same source key when the date differs', function () {
    AccountFacade::posting()->post(postingEntry(['date' => '2026-03-10']));

    expect(postingErrorOf(fn () => AccountFacade::posting()->post(postingEntry(['date' => '2026-03-11']))))->toBe(PostingError::DUPLICATE_SOURCE)
        ->and(Move::where('source_document_key', 'batch-1')->count())->toBe(1);
});

it('rejects the same source key when a line partner differs', function () {
    AccountFacade::posting()->post(postingEntry());

    $error = postingErrorOf(fn () => AccountFacade::posting()->post(postingEntry([
        'lines' => [
            new LineData(accountId: $this->debitAccount->id, debit: 100, label: 'Cash', partnerId: AccountHelper::partner()->id),
            new LineData(accountId: $this->creditAccount->id, credit: 100, label: 'Sales'),
        ],
    ])));

    expect($error)->toBe(PostingError::DUPLICATE_SOURCE);
});

it('rejects the same source key when the journal differs', function () {
    AccountFacade::posting()->post(postingEntry());

    $error = postingErrorOf(fn () => AccountFacade::posting()->post(postingEntry([
        'journalId' => AccountHelper::generalJournal()->id,
    ])));

    expect($error)->toBe(PostingError::DUPLICATE_SOURCE);
});

it('posts a foreign-currency entry whose currency amounts balance', function () {
    $foreign = AccountHelper::otherCurrency();

    $move = AccountFacade::posting()->post(postingEntry([
        'currencyId' => $foreign->id,
        'lines'      => [
            new LineData(accountId: $this->debitAccount->id, debit: 50, amountCurrency: 100),
            new LineData(accountId: $this->creditAccount->id, credit: 50, amountCurrency: -100),
        ],
    ]))->move;

    expect($move->currency_id)->toBe($foreign->id)
        ->and($move->lines->map(fn ($line) => (float) $line->amount_currency)->sort()->values()->all())->toBe([-100.0, 100.0]);
});

it('rejects a foreign-currency entry whose currency amounts do not balance and leaves nothing behind', function () {
    $foreign = AccountHelper::otherCurrency();

    $count = Move::count();

    $exception = postingExceptionOf(fn () => AccountFacade::posting()->post(postingEntry([
        'currencyId' => $foreign->id,
        'lines'      => [
            new LineData(accountId: $this->debitAccount->id, debit: 50, amountCurrency: 100),
            new LineData(accountId: $this->creditAccount->id, credit: 50, amountCurrency: -90),
        ],
    ])));

    expect($exception?->error)->toBe(PostingError::UNBALANCED)
        ->and($exception->details['debit'])->toEqualWithDelta(100.0, 0.00001)
        ->and($exception->details['credit'])->toEqualWithDelta(90.0, 0.00001)
        ->and(Move::count())->toBe($count);
});

it('reports an unexpected failure with a generic reason instead of the raw error', function () {
    AccountFacade::posting()->registerValidator(new class implements PostingValidator
    {
        public function validate(PostingContext $context): void
        {
            throw new RuntimeException('SQLSTATE[42S22]: internal detail');
        }
    });

    $count = Move::count();

    $exception = postingExceptionOf(fn () => AccountFacade::posting()->post(postingEntry()));

    expect($exception?->error)->toBe(PostingError::MOVE_NOT_POSTABLE)
        ->and($exception->details)->toBe(['reason' => __('accounts::system.posting.unexpected-error')])
        ->and($exception->getMessage())->not->toContain('SQLSTATE')
        ->and($exception->getPrevious())->toBeInstanceOf(RuntimeException::class)
        ->and(Move::count())->toBe($count);
});

it('keeps the translated rule message when a posting rule refuses the entry', function () {
    $entry = AccountHelper::journalEntry($this->journal);

    $exception = postingExceptionOf(fn () => AccountHelper::post($entry));

    expect($exception?->error)->toBe(PostingError::MOVE_NOT_POSTABLE)
        ->and($exception->getMessage())->toBe(__('accounts::account-manager.post-action-validate.lines-required'));
});

it('rejects a foreign-currency debit line whose currency amount is negative', function () {
    $error = postingExceptionOf(fn () => AccountFacade::posting()->post(postingEntry([
        'currencyId' => AccountHelper::otherCurrency()->id,
        'lines'      => [
            new LineData(accountId: $this->debitAccount->id, debit: 50, amountCurrency: -100),
            new LineData(accountId: $this->creditAccount->id, credit: 50, amountCurrency: 100),
        ],
    ])));

    expect($error?->error)->toBe(PostingError::INVALID_ENTRY)
        ->and($error->details)->toBe(['field' => 'lines.0']);
});

it('rejects a foreign-currency credit line whose currency amount is positive', function () {
    $error = postingExceptionOf(fn () => AccountFacade::posting()->post(postingEntry([
        'currencyId' => AccountHelper::otherCurrency()->id,
        'lines'      => [
            new LineData(accountId: $this->debitAccount->id, debit: 50, amountCurrency: 0),
            new LineData(accountId: $this->creditAccount->id, credit: 50, amountCurrency: 100),
        ],
    ])));

    expect($error?->error)->toBe(PostingError::INVALID_ENTRY)
        ->and($error->details)->toBe(['field' => 'lines.1']);
});

it('accepts a foreign-currency line with a company amount and a zero currency amount', function () {
    $result = AccountFacade::posting()->post(postingEntry([
        'currencyId' => AccountHelper::otherCurrency()->id,
        'lines'      => [
            new LineData(accountId: $this->debitAccount->id, debit: 0.01, amountCurrency: 0),
            new LineData(accountId: $this->creditAccount->id, credit: 0.01, amountCurrency: 0),
        ],
    ]));

    expect($result->move->state)->toBe(MoveState::POSTED);
});

it('reports a missing journal as an invalid journal with its own message', function () {
    $message = __('accounts::system.move.no-journal-found', ['company' => $this->company->name, 'types' => 'general']);

    AccountFacade::posting()->registerValidator(new class($message) implements PostingValidator
    {
        public function __construct(protected string $message) {}

        public function validate(PostingContext $context): void
        {
            throw new MissingJournalException($this->message);
        }
    });

    $exception = postingExceptionOf(fn () => AccountFacade::posting()->post(postingEntry()));

    expect($exception?->error)->toBe(PostingError::INVALID_ENTRY)
        ->and($exception->details)->toBe(['field' => 'journal_id'])
        ->and($exception->getMessage())->toBe($message);
});

it('reports a unique violation unrelated to the source key with a generic reason', function () {
    AccountFacade::posting()->registerValidator(new class implements PostingValidator
    {
        public function validate(PostingContext $context): void
        {
            throw new UniqueConstraintViolationException('testing', 'insert into accounts_account_moves', [], new RuntimeException('SQLSTATE[23000]: duplicate entry'));
        }
    });

    $count = Move::count();

    $exception = postingExceptionOf(fn () => AccountFacade::posting()->post(postingEntry()));

    expect($exception?->error)->toBe(PostingError::MOVE_NOT_POSTABLE)
        ->and($exception->details)->toBe(['reason' => __('accounts::system.posting.unexpected-error')])
        ->and($exception->getMessage())->not->toContain('SQLSTATE')
        ->and(Move::count())->toBe($count);
});
