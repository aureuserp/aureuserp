<?php

namespace Webkul\Account\Support\Posting;

use Carbon\CarbonImmutable;

class PostingClock
{
    public function today(): CarbonImmutable
    {
        return CarbonImmutable::now(config('app.timezone'))->startOfDay();
    }
}
