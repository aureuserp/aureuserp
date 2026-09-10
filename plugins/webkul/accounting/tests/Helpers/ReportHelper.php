<?php

use Livewire\Livewire;
use Webkul\Account\Enums\DisplayType;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Models\Account;
use Webkul\Account\Models\Journal;
use Webkul\Account\Models\Move;
use Webkul\Partner\Models\Partner;

require_once __DIR__.'/../../../accounts/tests/Helpers/AccountHelper.php';

class ReportHelper
{
    public static function data(string $page, string $method, array $state = []): array
    {
        $component = Livewire::test($page);

        foreach ($state as $key => $value) {
            $component->set("data.{$key}", $value);
        }

        return $component->instance()->{$method}();
    }

    public static function postedSale(string $date, float $qty, float $priceUnit, Account $account, ?Partner $partner = null, ?Journal $journal = null): Move
    {
        return static::postedMove(MoveType::OUT_INVOICE, $date, $qty, $priceUnit, $account, $partner, $journal);
    }

    public static function postedBill(string $date, float $qty, float $priceUnit, Account $account, ?Partner $partner = null, ?Journal $journal = null): Move
    {
        return static::postedMove(MoveType::IN_INVOICE, $date, $qty, $priceUnit, $account, $partner, $journal);
    }

    public static function postedSaleDueOn(string $date, string $dueDate, float $qty, float $priceUnit, Account $account, ?Partner $partner = null): Move
    {
        return static::postedDueOn(MoveType::OUT_INVOICE, $date, $dueDate, $qty, $priceUnit, $account, $partner);
    }

    public static function postedBillDueOn(string $date, string $dueDate, float $qty, float $priceUnit, Account $account, ?Partner $partner = null): Move
    {
        return static::postedDueOn(MoveType::IN_INVOICE, $date, $dueDate, $qty, $priceUnit, $account, $partner);
    }

    protected static function postedDueOn(MoveType $type, string $date, string $dueDate, float $qty, float $priceUnit, Account $account, ?Partner $partner): Move
    {
        $move = AccountHelper::invoice($type, $partner, null, [
            'invoice_date'     => $date,
            'date'             => $date,
            'invoice_date_due' => $dueDate,
        ]);

        AccountHelper::productLine($move, $account, qty: $qty, priceUnit: $priceUnit);

        $posted = AccountHelper::post($move);

        $posted->forceFill(['invoice_date_due' => $dueDate])->saveQuietly();

        return $posted->refresh();
    }

    public static function draftSale(string $date, float $qty, float $priceUnit, Account $account, ?Partner $partner = null): Move
    {
        return static::draftMove(MoveType::OUT_INVOICE, $date, $qty, $priceUnit, $account, $partner);
    }

    public static function draftBill(string $date, float $qty, float $priceUnit, Account $account, ?Partner $partner = null): Move
    {
        return static::draftMove(MoveType::IN_INVOICE, $date, $qty, $priceUnit, $account, $partner);
    }

    protected static function draftMove(MoveType $type, string $date, float $qty, float $priceUnit, Account $account, ?Partner $partner): Move
    {
        $move = AccountHelper::invoice($type, $partner, null, [
            'invoice_date' => $date,
            'date'         => $date,
        ]);

        AccountHelper::productLine($move, $account, qty: $qty, priceUnit: $priceUnit);

        return $move;
    }

    protected static function postedMove(MoveType $type, string $date, float $qty, float $priceUnit, Account $account, ?Partner $partner, ?Journal $journal): Move
    {
        $move = AccountHelper::invoice($type, $partner, $journal, [
            'invoice_date' => $date,
            'date'         => $date,
        ]);

        AccountHelper::productLine($move, $account, qty: $qty, priceUnit: $priceUnit);

        return AccountHelper::post($move);
    }

    public static function productAccountId(Move $move): int
    {
        return (int) $move->refresh()->lines
            ->firstWhere('display_type', DisplayType::PRODUCT)
            ->account_id;
    }

    public static function receivableAccountId(Move $move): int
    {
        return (int) $move->refresh()->lines
            ->firstWhere('display_type', DisplayType::PAYMENT_TERM)
            ->account_id;
    }

    public static function rowBy(iterable $rows, string $key, mixed $value): ?object
    {
        return collect($rows)->firstWhere($key, $value);
    }
}
