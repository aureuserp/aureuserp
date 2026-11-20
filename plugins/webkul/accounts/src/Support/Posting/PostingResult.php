<?php

namespace Webkul\Account\Support\Posting;

use Webkul\Account\Models\Move;

class PostingResult
{
    public function __construct(
        public readonly Move $move,
        public readonly bool $replayed = false,
    ) {}
}
