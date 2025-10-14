<?php

namespace Webkul\FullCalendar\Concerns;

trait InteractsWithRawJS
{
    public function eventClassNames(): string
    {
        return <<<'JS'
            null
        JS;
    }

    public function eventContent(): string
    {
        return <<<'JS'
            null
        JS;
    }

    public function eventDidMount(): string
    {
        return <<<'JS'
            null
        JS;
    }

    public function eventWillUnmount(): string
    {
        return <<<'JS'
            null
        JS;
    }
}
