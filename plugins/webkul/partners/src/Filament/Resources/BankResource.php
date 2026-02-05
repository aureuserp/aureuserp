<?php

namespace Webkul\Partner\Filament\Resources;

use Webkul\Partner\Models\Bank;
use Webkul\Support\Filament\Resources\BankResource as BaseBankResource;

class BankResource extends BaseBankResource
{
    protected static ?string $model = Bank::class;

    protected static bool $shouldRegisterNavigation = false;
}
