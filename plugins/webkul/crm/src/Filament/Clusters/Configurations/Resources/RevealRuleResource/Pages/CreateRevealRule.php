<?php

namespace Webkul\Crm\Filament\Clusters\Configurations\Resources\RevealRuleResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\RevealRuleResource;
use Webkul\Crm\Filament\Concerns\HasCRMClusterNavigation;

class CreateRevealRule extends CreateRecord
{
    use HasCRMClusterNavigation;

    protected static string $resource = RevealRuleResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
