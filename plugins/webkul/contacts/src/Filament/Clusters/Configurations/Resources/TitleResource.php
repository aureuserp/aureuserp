<?php

namespace Webkul\Contact\Filament\Clusters\Configurations\Resources;

use Webkul\Contact\Filament\Clusters\Configurations;
use Webkul\Contact\Filament\Clusters\Configurations\Resources\TitleResource\Pages\ManageTitles;
use Webkul\Partner\Filament\Resources\TitleResource as BaseTitleResource;

class TitleResource extends BaseTitleResource
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-academic-cap';

    protected static bool $shouldRegisterNavigation = true;

    protected static ?int $navigationSort = 2;

    protected static ?string $cluster = Configurations::class;

    public static function getPages(): array
    {
        return [
            'index' => ManageTitles::route('/'),
        ];
    }
}
