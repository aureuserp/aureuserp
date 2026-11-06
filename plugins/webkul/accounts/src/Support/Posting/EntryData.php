<?php

namespace Webkul\Account\Support\Posting;

use DateTimeInterface;

class EntryData
{
    public function __construct(
        public readonly int $companyId,
        public readonly int $userId,
        public readonly array $lines,
        public readonly DateTimeInterface|string|null $date = null,
        public readonly ?int $journalId = null,
        public readonly ?int $currencyId = null,
        public readonly ?string $reference = null,
        public readonly ?string $narration = null,
        public readonly ?string $sourceType = null,
        public readonly ?string $sourceKey = null,
    ) {}
}
