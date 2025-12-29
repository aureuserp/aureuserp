<?php

namespace Webkul\Accounting\Filament\Clusters\Configuration\Resources\JournalResource\Pages;

use Webkul\Account\Filament\Resources\JournalResource\Pages\ListJournals as BaseListJournals;
use Webkul\Accounting\Filament\Clusters\Configuration\Resources\JournalResource;

class ListJournals extends BaseListJournals
{
    protected static string $resource = JournalResource::class;
}
