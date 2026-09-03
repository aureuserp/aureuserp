<?php

namespace Webkul\Maintenance\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Field\Filament\Traits\HasCustomFields;
use Webkul\Maintenance\Filament\Resources\EquipmentResource\Pages\CreateEquipment;
use Webkul\Maintenance\Filament\Resources\EquipmentResource\Pages\EditEquipment;
use Webkul\Maintenance\Filament\Resources\EquipmentResource\Pages\ListEquipment;
use Webkul\Maintenance\Filament\Resources\EquipmentResource\Pages\ViewEquipment;
use Webkul\Maintenance\Filament\Resources\EquipmentResource\Schemas\EquipmentForm;
use Webkul\Maintenance\Filament\Resources\EquipmentResource\Schemas\EquipmentInfolist;
use Webkul\Maintenance\Filament\Resources\EquipmentResource\Tables\EquipmentTable;
use Webkul\Maintenance\Models\Equipment;
use Webkul\Support\Enums\NavigationGroup;

class EquipmentResource extends Resource
{
    use HasCustomFields;

    protected static ?string $model = Equipment::class;

    protected static ?string $slug = 'maintenance/equipments';

    protected static ?int $navigationSort = -1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getModelLabel(): string
    {
        return __('maintenance::models/equipment.title');
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Maintenance;
    }

    public static function getNavigationLabel(): string
    {
        return __('maintenance::filament/resources/equipment.navigation.title');
    }

    public static function form(Schema $schema): Schema
    {
        return EquipmentForm::configure($schema, static::getCustomFormFields());
    }

    public static function table(Table $table): Table
    {
        return EquipmentTable::configure($table, static::getCustomTableColumns(), static::getCustomTableFilters());
    }

    public static function infolist(Schema $schema): Schema
    {
        return EquipmentInfolist::configure($schema, static::getCustomInfolistEntries());
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListEquipment::route('/'),
            'create' => CreateEquipment::route('/create'),
            'view'   => ViewEquipment::route('/{record}'),
            'edit'   => EditEquipment::route('/{record}/edit'),
        ];
    }
}
