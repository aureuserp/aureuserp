<?php

namespace Webkul\Inventory;

use Webkul\Core\Contracts\Plugin;

class InventoryPlugin implements Plugin
{
    public function getName(): string
    {
        return 'inventory';
    }

    public function getDisplayName(): string
    {
        return 'Inventory';
    }

    public function getDescription(): string
    {
        return 'Manage inventory functionality';
    }

    public function getVersion(): string
    {
        return '1.0.0';
    }

    public function getAuthor(): string
    {
        return 'Webkul';
    }

    public function getDependencies(): array
    {
        return ['website'];
    }
}