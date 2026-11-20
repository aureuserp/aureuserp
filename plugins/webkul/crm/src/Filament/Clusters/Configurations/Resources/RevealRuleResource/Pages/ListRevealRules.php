<?php

namespace Webkul\Crm\Filament\Clusters\Configurations\Resources\RevealRuleResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\RevealRuleResource;
use Webkul\Crm\Filament\Concerns\HasCRMClusterNavigation;

class ListRevealRules extends ListRecords
{
    use HasCRMClusterNavigation;

    protected static string $resource = RevealRuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->icon('heroicon-o-plus-circle'),
        ];
    }
}
