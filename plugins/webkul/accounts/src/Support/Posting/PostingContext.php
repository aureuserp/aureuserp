<?php

namespace Webkul\Account\Support\Posting;

use Carbon\CarbonImmutable;
use Webkul\Account\Enums\PostingOperation;
use Webkul\Account\Models\Move;

class PostingContext
{
    public function __construct(
        public readonly PostingOperation $operation,
        public readonly Move $move,
        public readonly CarbonImmutable $date,
        public readonly ?int $userId,
    ) {}
}
