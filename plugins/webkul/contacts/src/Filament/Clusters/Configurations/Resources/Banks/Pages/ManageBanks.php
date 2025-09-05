<?php

namespace Webkul\Contact\Filament\Clusters\Configurations\Resources\Banks\Pages;

use Webkul\Contact\Filament\Clusters\Configurations\Resources\Banks\BankResource;
use Webkul\Partner\Filament\Resources\Banks\Pages\ManageBanks as BaseManageBanks;

class ManageBanks extends BaseManageBanks
{
    protected static string $resource = BankResource::class;
}
