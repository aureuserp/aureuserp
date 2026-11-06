<?php

namespace Webkul\Account\Support\Posting;

class LineData
{
    public function __construct(
        public readonly int $accountId,
        public readonly float $debit = 0.0,
        public readonly float $credit = 0.0,
        public readonly ?string $label = null,
        public readonly ?int $partnerId = null,
        public readonly ?float $amountCurrency = null,
        public readonly array $taxIds = [],
        public readonly ?int $taxLineId = null,
    ) {}
}
