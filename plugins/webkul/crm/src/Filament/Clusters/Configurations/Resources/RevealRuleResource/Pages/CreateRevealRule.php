<?php

namespace Webkul\Crm\Filament\Clusters\Configurations\Resources\RevealRuleResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\RevealRuleResource;
use Webkul\Crm\Filament\Concerns\HasCrmClusterNavigation;

class CreateRevealRule extends CreateRecord
{
    use HasCrmClusterNavigation;

    protected static string $resource = RevealRuleResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
