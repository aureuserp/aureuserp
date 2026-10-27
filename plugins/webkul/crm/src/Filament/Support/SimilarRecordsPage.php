<?php

namespace Webkul\Crm\Filament\Support;

use Filament\Resources\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;

abstract class SimilarRecordsPage extends Page implements HasTable
{
    use InteractsWithTable;
}
