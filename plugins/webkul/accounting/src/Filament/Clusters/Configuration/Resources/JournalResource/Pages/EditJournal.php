<?php

namespace Webkul\Accounting\Filament\Clusters\Configuration\Resources\JournalResource\Pages;

use Webkul\Account\Filament\Resources\JournalResource\Pages\EditJournal as BaseEditJournal;
use Webkul\Accounting\Filament\Clusters\Configuration\Resources\JournalResource;

class EditJournal extends BaseEditJournal
{
    protected static string $resource = JournalResource::class;
}
