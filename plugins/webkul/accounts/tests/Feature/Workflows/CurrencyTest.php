<?php

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Webkul\Account\Contracts\PostingValidator;
use Webkul\Account\Enums\DisplayType;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Enums\PaymentState;
use Webkul\Account\Enums\PostingOperation;
use Webkul\Account\Exceptions\PostingRejectedException;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Account\Models\Move;
use Webkul\Account\Models\PartialReconcile;
use Webkul\Account\Support\Posting\PostingClock;
use Webkul\Account\Support\Posting\PostingContext;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;
use Webkul\Support\Models\Currency;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../Helpers/AccountHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('accounts');

    DB::table('plugins')->updateOrInsert(
        ['name' => 'accounts'],
        ['is_installed' => true, 'is_active' => true, 'updated_at' => now()],
    );

    Package::$plugins = Plugin::all()->keyBy('name');

    URL::resolveMissingNamedRoutesUsing(fn () => '#');

    AccountHelper::actingAsAdmin();

    $this->income = AccountHelper::account('income');
    $this->partner = AccountHelper::partner();

    $this->foreign = AccountHelper::otherCurrency();
    $this->foreign->rates()->delete();
    AccountHelper::setCurrencyRate($this->foreign, 2.0);
});

it('converts nothing when both currencies are the same', function () {
    $company = AccountHelper::company();

    expect($this->foreign->getConversionRate($this->foreign, $this->foreign, $company))->toBe(1);
});

it('converts from company currency into a foreign currency at the foreign rate', function () {
    $company = AccountHelper::company();
    $base = $company->currency;

    expect($base->convert(100, $this->foreign, $company))->toBe(200.0);
});

it('converts from a foreign currency back into company currency', function () {
    $company = AccountHelper::company();
    $base = $company->currency;

    expect($this->foreign->convert(200, $base, $company))->toBe(100.0);
});

it('converts between two foreign currencies using both rates', function () {
    $company = AccountHelper::company();

    $other = Currency::query()
        ->whereNotIn('id', [$company->currency_id, $this->foreign->id])
        ->firstOrFail();

    $other->rates()->delete();

    AccountHelper::setCurrencyRate($other, 8.0);

    expect($this->foreign->convert(100, $other, $company))->toBe(400.0);
});

it('returns to the original amount after a round trip', function () {
    $company = AccountHelper::company();
    $base = $company->currency;

    $converted = $base->convert(250, $this->foreign, $company);

    expect($this->foreign->convert($converted, $base, $company))->toBe(250.0);
});

it('totals a foreign-currency invoice in its own currency', function () {
    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, $this->partner, null, ['currency_id' => $this->foreign->id]);
    AccountHelper::productLine($invoice, $this->income, qty: 2, priceUnit: 100);

    AccountHelper::compute($invoice);

    expect((float) $invoice->refresh()->amount_total)->toBe(200.0);
});

it('records the foreign amount separately from the company-currency balance', function () {
    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, $this->partner, null, ['currency_id' => $this->foreign->id]);
    AccountHelper::productLine($invoice, $this->income, qty: 2, priceUnit: 100);

    AccountHelper::post($invoice);

    $productLine = $invoice->refresh()->lines->firstWhere('display_type', DisplayType::PRODUCT);

    expect((float) abs($productLine->amount_currency))->toBe(200.0)
        ->and((float) abs($productLine->balance))->toBe(100.0);
});

it('keeps a foreign-currency invoice balanced in company currency', function () {
    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, $this->partner, null, ['currency_id' => $this->foreign->id]);
    AccountHelper::productLine($invoice, $this->income, qty: 2, priceUnit: 100);

    AccountHelper::post($invoice);

    $lines = $invoice->refresh()->lines;

    expect((float) $lines->sum(fn ($l) => (float) $l->debit))
        ->toBe((float) $lines->sum(fn ($l) => (float) $l->credit));
});

it('creates an exchange-difference entry when reconciling foreign documents at different rates', function () {
    AccountHelper::setCurrencyRate($this->foreign, 4.0, now()->toDateString());

    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, $this->partner, null, [
        'currency_id'  => $this->foreign->id,
        'invoice_date' => now()->subYear()->toDateString(),
        'date'         => now()->subYear()->toDateString(),
    ]);
    AccountHelper::productLine($invoice, $this->income, qty: 2, priceUnit: 100);
    AccountHelper::post($invoice);

    $creditNote = AccountHelper::invoice(MoveType::OUT_REFUND, $this->partner, null, [
        'currency_id'  => $this->foreign->id,
        'invoice_date' => now()->toDateString(),
        'date'         => now()->toDateString(),
    ]);
    AccountHelper::productLine($creditNote, $this->income, qty: 2, priceUnit: 100);
    AccountHelper::post($creditNote);

    AccountHelper::reconcile($invoice, $creditNote);

    expect((float) abs($invoice->refresh()->amount_residual))->toBe(0.0)
        ->and(Move::where('move_type', MoveType::ENTRY)->count())->toBeGreaterThan(0);
});

it('leaves the documents unreconciled when the exchange-difference entry is rejected', function () {
    AccountHelper::setCurrencyRate($this->foreign, 4.0, now()->toDateString());

    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, $this->partner, null, [
        'currency_id'  => $this->foreign->id,
        'invoice_date' => now()->subYear()->toDateString(),
        'date'         => now()->subYear()->toDateString(),
    ]);
    AccountHelper::productLine($invoice, $this->income, qty: 2, priceUnit: 100);
    AccountHelper::post($invoice);

    $creditNote = AccountHelper::invoice(MoveType::OUT_REFUND, $this->partner, null, [
        'currency_id'  => $this->foreign->id,
        'invoice_date' => now()->toDateString(),
        'date'         => now()->toDateString(),
    ]);
    AccountHelper::productLine($creditNote, $this->income, qty: 2, priceUnit: 100);
    AccountHelper::post($creditNote);

    $residual = (float) $invoice->refresh()->amount_residual;

    AccountFacade::posting()->registerValidator(new class implements PostingValidator
    {
        public function validate(PostingContext $context): void
        {
            throw new PostingRejectedException('closed period');
        }
    });

    expect(fn () => AccountHelper::reconcile($invoice, $creditNote))->toThrow(PostingRejectedException::class)
        ->and((float) $invoice->refresh()->amount_residual)->toBe($residual)
        ->and(PartialReconcile::count())->toBe(0);
});

it('keeps the reconciliation when reversing its exchange-difference entry is rejected', function () {
    AccountHelper::setCurrencyRate($this->foreign, 4.0, now()->toDateString());

    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, $this->partner, null, [
        'currency_id'  => $this->foreign->id,
        'invoice_date' => now()->subYear()->toDateString(),
        'date'         => now()->subYear()->toDateString(),
    ]);
    AccountHelper::productLine($invoice, $this->income, qty: 2, priceUnit: 100);
    AccountHelper::post($invoice);

    $creditNote = AccountHelper::invoice(MoveType::OUT_REFUND, $this->partner, null, [
        'currency_id'  => $this->foreign->id,
        'invoice_date' => now()->toDateString(),
        'date'         => now()->toDateString(),
    ]);
    AccountHelper::productLine($creditNote, $this->income, qty: 2, priceUnit: 100);
    AccountHelper::post($creditNote);

    AccountHelper::reconcile($invoice, $creditNote);

    $partial = PartialReconcile::whereNotNull('exchange_move_id')->latest('id')->firstOrFail();

    AccountFacade::posting()->registerValidator(new class implements PostingValidator
    {
        public function validate(PostingContext $context): void
        {
            if ($context->operation === PostingOperation::REVERSE) {
                throw new PostingRejectedException('closed period');
            }
        }
    });

    expect(fn () => AccountFacade::unReconcile($partial))->toThrow(PostingRejectedException::class)
        ->and(PartialReconcile::whereKey($partial->id)->exists())->toBeTrue()
        ->and(Move::where('reversed_entry_id', $partial->exchange_move_id)->exists())->toBeFalse();
});

function reconciledWithExchangeDifference(object $test): array
{
    AccountHelper::setCurrencyRate($test->foreign, 4.0, now()->toDateString());

    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, $test->partner, null, [
        'currency_id'  => $test->foreign->id,
        'invoice_date' => now()->subYear()->toDateString(),
        'date'         => now()->subYear()->toDateString(),
    ]);
    AccountHelper::productLine($invoice, $test->income, qty: 2, priceUnit: 100);
    AccountHelper::post($invoice);

    $creditNote = AccountHelper::invoice(MoveType::OUT_REFUND, $test->partner, null, [
        'currency_id'  => $test->foreign->id,
        'invoice_date' => now()->toDateString(),
        'date'         => now()->toDateString(),
    ]);
    AccountHelper::productLine($creditNote, $test->income, qty: 2, priceUnit: 100);
    AccountHelper::post($creditNote);

    AccountHelper::reconcile($invoice, $creditNote);

    return [$invoice, PartialReconcile::whereNotNull('exchange_move_id')->latest('id')->firstOrFail()];
}

function reversalDateValidator(?CarbonImmutable $acceptedDate): PostingValidator
{
    return new class($acceptedDate) implements PostingValidator
    {
        public array $dates = [];

        public function __construct(protected ?CarbonImmutable $acceptedDate) {}

        public function validate(PostingContext $context): void
        {
            if ($context->operation !== PostingOperation::REVERSE) {
                return;
            }

            $this->dates[] = $context->date;

            if (! $this->acceptedDate || ! $context->date->equalTo($this->acceptedDate)) {
                throw new PostingRejectedException('closed period');
            }
        }
    };
}

it('keeps the exchange entry date when undoing a reconciliation the validator accepts', function () {
    [, $partial] = reconciledWithExchangeDifference($this);

    $exchangeDate = CarbonImmutable::parse($partial->exchangeMove->date)->startOfDay();

    $validator = reversalDateValidator($exchangeDate);

    AccountFacade::posting()->registerValidator($validator);

    AccountFacade::unReconcile($partial);

    expect($validator->dates)->toHaveCount(1)
        ->and(PartialReconcile::whereKey($partial->id)->exists())->toBeFalse()
        ->and(Move::where('reversed_entry_id', $partial->exchange_move_id)->exists())->toBeTrue();
});

it('falls back to the clock date when the exchange entry date is rejected and honours a rebound clock', function () {
    [, $partial] = reconciledWithExchangeDifference($this);

    $fixedToday = CarbonImmutable::parse(now()->addDays(3)->toDateString())->startOfDay();

    app()->instance(PostingClock::class, new class($fixedToday) extends PostingClock
    {
        public function __construct(protected CarbonImmutable $fixed) {}

        public function today(): CarbonImmutable
        {
            return $this->fixed;
        }
    });

    $validator = reversalDateValidator($fixedToday);

    AccountFacade::posting()->registerValidator($validator);

    AccountFacade::unReconcile($partial);

    $reversal = Move::where('reversed_entry_id', $partial->exchange_move_id)->firstOrFail();

    expect($validator->dates)->toHaveCount(2)
        ->and($validator->dates[1]->equalTo($fixedToday))->toBeTrue()
        ->and(CarbonImmutable::parse($reversal->date)->toDateString())->toBe($fixedToday->toDateString())
        ->and(PartialReconcile::whereKey($partial->id)->exists())->toBeFalse();
});

it('fails the undo and keeps the reconciliation when both the original and the clock date are rejected', function () {
    [, $partial] = reconciledWithExchangeDifference($this);

    $validator = reversalDateValidator(null);

    AccountFacade::posting()->registerValidator($validator);

    expect(fn () => AccountFacade::unReconcile($partial))->toThrow(PostingRejectedException::class)
        ->and($validator->dates)->toHaveCount(2)
        ->and(PartialReconcile::whereKey($partial->id)->exists())->toBeTrue()
        ->and(Move::where('reversed_entry_id', $partial->exchange_move_id)->exists())->toBeFalse();
});

it('does not fall back to the clock date for errors other than a validator rejection', function () {
    [, $partial] = reconciledWithExchangeDifference($this);

    $validator = new class implements PostingValidator
    {
        public int $calls = 0;

        public function validate(PostingContext $context): void
        {
            if ($context->operation === PostingOperation::REVERSE) {
                $this->calls++;

                throw new RuntimeException('boom');
            }
        }
    };

    AccountFacade::posting()->registerValidator($validator);

    expect(fn () => AccountFacade::unReconcile($partial))->toThrow(RuntimeException::class)
        ->and($validator->calls)->toBe(1);
});

it('blocks reset to draft before unreconciling anything when the validator rejects it', function () {
    [$invoice, $partial] = reconciledWithExchangeDifference($this);

    $validator = rejectingOperationValidator(PostingOperation::RESET_TO_DRAFT);

    AccountFacade::posting()->registerValidator($validator);

    expect(fn () => AccountHelper::resetToDraft($invoice))->toThrow(PostingRejectedException::class)
        ->and(collect($validator->operations)->first())->toBe(PostingOperation::RESET_TO_DRAFT)
        ->and(PartialReconcile::whereKey($partial->id)->exists())->toBeTrue()
        ->and(Move::where('reversed_entry_id', $partial->exchange_move_id)->exists())->toBeFalse()
        ->and($invoice->refresh()->state)->toBe(MoveState::POSTED);
});

it('blocks cancelling a draft entry when the validator rejects it', function () {
    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, $this->partner);
    AccountHelper::productLine($invoice, $this->income, qty: 1, priceUnit: 50);

    $validator = rejectingOperationValidator(PostingOperation::CANCEL);

    AccountFacade::posting()->registerValidator($validator);

    expect(fn () => AccountHelper::cancel($invoice))->toThrow(PostingRejectedException::class)
        ->and($validator->operations)->toBe([PostingOperation::CANCEL])
        ->and($invoice->refresh()->state)->toBe(MoveState::DRAFT);
});

function rejectingOperationValidator(PostingOperation $operation): PostingValidator
{
    return new class($operation) implements PostingValidator
    {
        public array $operations = [];

        public function __construct(protected PostingOperation $operation) {}

        public function validate(PostingContext $context): void
        {
            $this->operations[] = $context->operation;

            if ($context->operation === $this->operation) {
                throw new PostingRejectedException('closed period');
            }
        }
    };
}

it('keeps every reconciliation when resetting to draft is stopped by a rejected exchange reversal', function () {
    AccountHelper::setCurrencyRate($this->foreign, 4.0, now()->toDateString());

    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, $this->partner, null, [
        'currency_id'  => $this->foreign->id,
        'invoice_date' => now()->subYear()->toDateString(),
        'date'         => now()->subYear()->toDateString(),
    ]);
    AccountHelper::productLine($invoice, $this->income, qty: 2, priceUnit: 100);
    AccountHelper::post($invoice);

    foreach ([1, 2] as $index) {
        $creditNote = AccountHelper::invoice(MoveType::OUT_REFUND, $this->partner, null, [
            'currency_id'  => $this->foreign->id,
            'invoice_date' => now()->toDateString(),
            'date'         => now()->toDateString(),
        ]);
        AccountHelper::productLine($creditNote, $this->income, qty: 1, priceUnit: 100);
        AccountHelper::post($creditNote);

        AccountHelper::reconcile($invoice, $creditNote);
    }

    $invoiceLineIds = $invoice->refresh()->lines->pluck('id');

    $partials = PartialReconcile::query()
        ->where(fn ($query) => $query->whereIn('debit_move_id', $invoiceLineIds)->orWhereIn('credit_move_id', $invoiceLineIds))
        ->orderBy('id')
        ->get();

    expect($partials->count())->toBeGreaterThanOrEqual(2)
        ->and($partials->whereNotNull('exchange_move_id')->count())->toBeGreaterThanOrEqual(1)
        ->and($partials->first()->exchange_move_id)->toBeNull();

    AccountFacade::posting()->registerValidator(new class implements PostingValidator
    {
        public function validate(PostingContext $context): void
        {
            if ($context->operation === PostingOperation::REVERSE) {
                throw new PostingRejectedException('closed period');
            }
        }
    });

    expect(fn () => AccountHelper::resetToDraft($invoice))->toThrow(PostingRejectedException::class)
        ->and(PartialReconcile::whereKey($partials->pluck('id'))->count())->toBe($partials->count())
        ->and(Move::whereIn('reversed_entry_id', $partials->pluck('exchange_move_id')->filter())->exists())->toBeFalse()
        ->and($invoice->refresh()->state)->toBe(MoveState::POSTED);
});

it('marks a foreign-currency invoice paid when settled in its own currency', function () {
    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, $this->partner, null, ['currency_id' => $this->foreign->id]);
    AccountHelper::productLine($invoice, $this->income, qty: 2, priceUnit: 100);
    AccountHelper::post($invoice);

    AccountHelper::pay($invoice);

    $receivable = $invoice->refresh()->lines->firstWhere('display_type', DisplayType::PAYMENT_TERM);

    expect($invoice->payment_state)->toBe(PaymentState::PAID)
        ->and((float) abs($invoice->amount_residual))->toBe(0.0)
        ->and((float) abs($receivable->amount_residual_currency))->toBe(0.0);
});
