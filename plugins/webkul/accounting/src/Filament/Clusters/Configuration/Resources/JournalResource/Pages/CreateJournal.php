<?php

namespace Webkul\Accounting\Filament\Clusters\Configuration\Resources\JournalResource\Pages;

use Webkul\Account\Filament\Resources\JournalResource\Pages\CreateJournal as BaseCreateJournal;
use Webkul\Accounting\Filament\Clusters\Configuration\Resources\JournalResource;

class CreateJournal extends BaseCreateJournal
{
    protected static string $resource = JournalResource::class;
}
