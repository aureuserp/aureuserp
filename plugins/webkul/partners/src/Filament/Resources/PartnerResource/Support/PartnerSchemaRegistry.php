<?php

namespace Webkul\Partner\Filament\Resources\PartnerResource\Support;

use Webkul\Support\Filament\Contributions\AbstractSchemaRegistry;

class PartnerSchemaRegistry extends AbstractSchemaRegistry
{
    protected static function scope(): string
    {
        return 'partner';
    }
}
