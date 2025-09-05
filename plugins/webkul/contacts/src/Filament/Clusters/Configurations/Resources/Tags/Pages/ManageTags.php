<?php

namespace Webkul\Contact\Filament\Clusters\Configurations\Resources\Tags\Pages;

use Webkul\Contact\Filament\Clusters\Configurations\Resources\Tags\TagResource;
use Webkul\Partner\Filament\Resources\Tags\Pages\ManageTags as BaseManageTags;

class ManageTags extends BaseManageTags
{
    protected static string $resource = TagResource::class;
}
