<?php

namespace Webkul\Website\Filament\Customer\Resources;

use Filament\Resources\Resource;
use Webkul\Website\Filament\Customer\Resources\PageResource\Pages;
use Webkul\Website\Models\Page;
use Illuminate\Database\Eloquent\Model;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static ?string $recordRouteKeyName = 'slug';

    protected static bool $shouldRegisterNavigation = false;

    public static function canAccess(): bool
    {
        return true;
    }

    public static function canView(Model $record): bool
    {
        return true;
    }

    public static function getPages(): array
    {
        return [
            'view' => Pages\ViewPage::route('/{record}'),
        ];
    }
}
