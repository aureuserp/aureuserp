<?php

namespace Webkul\Support\Filament\Resources\ActivityTypes;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Support\Filament\Resources\ActivityTypes\Pages\CreateActivityType;
use Webkul\Support\Filament\Resources\ActivityTypes\Pages\EditActivityType;
use Webkul\Support\Filament\Resources\ActivityTypes\Pages\ListActivityTypes;
use Webkul\Support\Filament\Resources\ActivityTypes\Pages\ViewActivityType;
use Webkul\Support\Filament\Resources\ActivityTypes\Schemas\ActivityTypeForm;
use Webkul\Support\Filament\Resources\ActivityTypes\Schemas\ActivityTypeInfolist;
use Webkul\Support\Filament\Resources\ActivityTypes\Tables\ActivityTypesTable;
use Webkul\Support\Models\ActivityType;

class ActivityTypeResource extends Resource
{
    protected static ?string $model = ActivityType::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $slug = 'settings/activity-types';

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return ActivityTypeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ActivityTypesTable::configure($table);
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
