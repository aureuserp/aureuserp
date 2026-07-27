<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Boot the app and install the ERP schema before DatabaseTransactions (registered
     * via Pest.php) opens its per-test transaction in parent::setUp(). On PostgreSQL,
     * DDL is transactional, so running migrate:fresh/plugin installs inside that
     * transaction would have them rolled back as soon as the first test finishes.
     */
    protected function setUp(): void
    {
        if (! $this->app) {
            $this->refreshApplication();
        }

        \TestBootstrapHelper::ensureAllPluginsInstalled();

        parent::setUp();
    }
}
