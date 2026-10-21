<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Webkul\Support\Filament\Contributions\SchemaRegistry;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        SchemaRegistry::flush();

        if (! $this->app) {
            $this->refreshApplication();
        }

        \TestBootstrapHelper::ensureERPInstalled();

        parent::setUp();
    }
}
