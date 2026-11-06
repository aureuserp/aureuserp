<?php

namespace Webkul\Account\Support\Posting;

use DateTimeInterface;

class ReversalData
{
    public function __construct(
        public readonly int $companyId,
        public readonly int $userId,
        public readonly int $moveId,
        public readonly DateTimeInterface|string|null $date = null,
        public readonly ?string $reference = null,
        public readonly ?string $sourceType = null,
        public readonly ?string $sourceKey = null,
    ) {}
}
