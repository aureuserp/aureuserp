<?php

namespace Webkul\FullCalendar\Contracts;

interface HasRawJs
{
    public function eventClassNames(): string;

    public function eventContent(): string;

    public function eventDidMount(): string;

    public function eventWillUnmount(): string;
}
