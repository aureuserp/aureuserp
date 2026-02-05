<?php

namespace Webkul\Inventory\Models;

class Dropship extends Operation
{
    public function getModelTitle(): string
    {
        return __('inventories::models/dropship.title');
    }
}
