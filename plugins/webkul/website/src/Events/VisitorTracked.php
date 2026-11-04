<?php

namespace Webkul\Website\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Webkul\Website\Models\Page;
use Webkul\Website\Models\Visitor;

class VisitorTracked
{
    use Dispatchable;

    public function __construct(
        public Visitor $visitor,
        public ?string $ip,
        public string $url,
        public ?Page $page = null,
    ) {}
}
