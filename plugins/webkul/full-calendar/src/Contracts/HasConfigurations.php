<?php

namespace Webkul\FullCalendar\Contracts;

interface HasConfigurations
{
    public function config(): array;

    public function getConfig(): array;
}
