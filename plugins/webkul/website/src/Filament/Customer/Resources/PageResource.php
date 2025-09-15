<?php

namespace Webkul\Website\Filament\Customer\Resources;

use Filament\Resources\Resource;
use Webkul\Website\Filament\Customer\Resources\PageResource\Pages\ViewPage;
use Webkul\Website\Models\Page;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static ?string $recordRouteKeyName = 'slug';

    protected static bool $shouldRegisterNavigation = false;

    protected static bool $shouldSkipAuthorization = true;

    public static function getPages(): array
    {
        return [
            'view' => ViewPage::route('/{record}'),
        ];
    }
}
