<?php

namespace Webkul\Support\Filament\Resources;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Support\Filament\Clusters\Settings;
use Webkul\Support\Filament\Resources\ActivityTypeResource\Pages\CreateActivityType;
use Webkul\Support\Filament\Resources\ActivityTypeResource\Pages\EditActivityType;
use Webkul\Support\Filament\Resources\ActivityTypeResource\Pages\ListActivityTypes;
use Webkul\Support\Filament\Resources\ActivityTypeResource\Pages\ViewActivityType;
use Webkul\Support\Filament\Resources\ActivityTypeResource\Schemas\ActivityTypeForm;
use Webkul\Support\Filament\Resources\ActivityTypeResource\Schemas\ActivityTypeInfolist;
use Webkul\Support\Filament\Resources\ActivityTypeResource\Tables\ActivityTypesTable;
use Webkul\Support\Models\ActivityType;

class ActivityTypeResource extends Resource
{
    protected static ?string $model = ActivityType::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $slug = 'activity-types';

    protected static ?string $cluster = Settings::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $pluginName = 'support';

    public static function form(Schema $schema): Schema
    {
        return ActivityTypeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ActivityTypesTable::configure($table, static::$pluginName);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ActivityTypeInfolist::configure($schema);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListActivityTypes::route('/'),
            'create' => CreateActivityType::route('/create'),
            'view'   => ViewActivityType::route('/{record}'),
            'edit'   => EditActivityType::route('/{record}/edit'),
        ];
    }
}
