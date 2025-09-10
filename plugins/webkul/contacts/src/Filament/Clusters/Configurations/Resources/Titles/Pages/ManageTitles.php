<?php

namespace Webkul\Contact\Filament\Clusters\Configurations\Resources\Titles\Pages;

use Webkul\Contact\Filament\Clusters\Configurations\Resources\Titles\TitleResource;
use Webkul\Partner\Filament\Resources\Titles\Pages\ManageTitles as BaseManageTitles;

class ManageTitles extends BaseManageTitles
{
    protected static string $resource = TitleResource::class;
}
