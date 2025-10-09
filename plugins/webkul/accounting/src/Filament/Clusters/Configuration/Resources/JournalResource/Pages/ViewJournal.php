<?php

namespace Webkul\Accounting\Filament\Clusters\Configuration\Resources\JournalResource\Pages;

use Webkul\Accounting\Filament\Clusters\Configuration\Resources\JournalResource;
use Webkul\Account\Filament\Resources\JournalResource\Pages\ViewJournal as BaseViewJournal;

class ViewJournal extends BaseViewJournal
{
    protected static string $resource = JournalResource::class;
}
