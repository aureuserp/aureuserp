<?php

namespace Webkul\Accounting\Filament\Clusters\Configuration\Resources\JournalResource\Pages;

use Webkul\Accounting\Filament\Clusters\Configuration\Resources\JournalResource;
use Webkul\Account\Filament\Resources\JournalResource\Pages\CreateJournal as BaseCreateJournal;

class CreateJournal extends BaseCreateJournal
{
    protected static string $resource = JournalResource::class;
}
