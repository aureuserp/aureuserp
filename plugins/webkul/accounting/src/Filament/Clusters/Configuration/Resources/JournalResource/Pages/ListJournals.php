<?php

namespace Webkul\Accounting\Filament\Clusters\Configuration\Resources\JournalResource\Pages;

use Webkul\Accounting\Filament\Clusters\Configuration\Resources\JournalResource;
use Webkul\Account\Filament\Resources\JournalResource\Pages\ListJournals as BaseListJournals;

class ListJournals extends BaseListJournals
{
    protected static string $resource = JournalResource::class;
}
