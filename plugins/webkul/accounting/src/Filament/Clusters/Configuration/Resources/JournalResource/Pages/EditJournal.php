<?php

namespace Webkul\Accounting\Filament\Clusters\Configuration\Resources\JournalResource\Pages;

use Webkul\Accounting\Filament\Clusters\Configuration\Resources\JournalResource;
use Webkul\Account\Filament\Resources\JournalResource\Pages\EditJournal as BaseEditJournal;

class EditJournal extends BaseEditJournal
{
    protected static string $resource = JournalResource::class;
}
