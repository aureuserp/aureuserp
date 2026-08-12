<?php

namespace Webkul\Maintenance\Filament\Clusters\Maintenance\Resources;

use BackedEnum;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Field\Filament\Traits\HasCustomFields;
use Webkul\Maintenance\Filament\Clusters\Maintenance;
use Webkul\Maintenance\Filament\Clusters\Maintenance\Resources\MaintenanceRequestResource\Pages\CreateMaintenanceRequest;
use Webkul\Maintenance\Filament\Clusters\Maintenance\Resources\MaintenanceRequestResource\Pages\EditMaintenanceRequest;
use Webkul\Maintenance\Filament\Clusters\Maintenance\Resources\MaintenanceRequestResource\Pages\ListMaintenanceRequests;
use Webkul\Maintenance\Filament\Clusters\Maintenance\Resources\MaintenanceRequestResource\Pages\ViewMaintenanceRequest;
use Webkul\Maintenance\Filament\Clusters\Maintenance\Resources\MaintenanceRequestResource\Schemas\MaintenanceRequestForm;
use Webkul\Maintenance\Filament\Clusters\Maintenance\Resources\MaintenanceRequestResource\Schemas\MaintenanceRequestInfolist;
use Webkul\Maintenance\Filament\Clusters\Maintenance\Resources\MaintenanceRequestResource\Tables\MaintenanceRequestsTable;
use Webkul\Maintenance\Models\MaintenanceRequest;

class MaintenanceRequestResource extends Resource
{
    use HasCustomFields;

    protected static ?string $model = MaintenanceRequest::class;

    protected static ?string $cluster = Maintenance::class;

    protected static ?string $slug = 'requests';

    protected static ?int $navigationSort = 0;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static ?string $recordTitleAttribute = 'name';

    public static function getModelLabel(): string
    {
        return __('maintenance::models/maintenance-request.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('maintenance::filament/clusters/maintenance/resources/maintenance-request.navigation.title');
    }

    public static function form(Schema $schema): Schema
    {
        return MaintenanceRequestForm::configure($schema, static::getCustomFormFields());
    }

    public static function table(Table $table): Table
    {
        return MaintenanceRequestsTable::configure($table, static::getCustomTableColumns());
    }

    public static function infolist(Schema $schema): Schema
    {
        return MaintenanceRequestInfolist::configure($schema, static::getCustomInfolistEntries());
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListMaintenanceRequests::route('/'),
            'create' => CreateMaintenanceRequest::route('/create'),
            'view'   => ViewMaintenanceRequest::route('/{record}'),
            'edit'   => EditMaintenanceRequest::route('/{record}/edit'),
        ];
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewMaintenanceRequest::class,
            EditMaintenanceRequest::class,
        ]);
    }
}
