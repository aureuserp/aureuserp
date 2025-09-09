<?php

namespace Webkul\TimeOff\Filament\Clusters\Configurations\Resources\LeaveTypes;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\TimeOff\Filament\Clusters\Configurations;
use Webkul\TimeOff\Filament\Clusters\Configurations\Resources\LeaveTypes\Pages\CreateLeaveType;
use Webkul\TimeOff\Filament\Clusters\Configurations\Resources\LeaveTypes\Pages\EditLeaveType;
use Webkul\TimeOff\Filament\Clusters\Configurations\Resources\LeaveTypes\Pages\ListLeaveTypes;
use Webkul\TimeOff\Filament\Clusters\Configurations\Resources\LeaveTypes\Pages\ViewLeaveType;
use Webkul\TimeOff\Filament\Clusters\Configurations\Resources\LeaveTypes\Schemas\LeaveTypeForm;
use Webkul\TimeOff\Filament\Clusters\Configurations\Resources\LeaveTypes\Schemas\LeaveTypeInfolist;
use Webkul\TimeOff\Filament\Clusters\Configurations\Resources\LeaveTypes\Tables\LeaveTypesTable;
use Webkul\TimeOff\Models\LeaveType;

class LeaveTypeResource extends Resource
{
    protected static ?string $model = LeaveType::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $cluster = Configurations::class;

    protected static ?int $navigationSort = 1;

    public static function getModelLabel(): string
    {
        return __('time-off::filament/clusters/configurations/resources/leave-type.title');
    }

    public static function form(Schema $schema): Schema
    {
        return LeaveTypeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LeaveTypesTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return LeaveTypeInfolist::configure($schema);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListLeaveTypes::route('/'),
            'create' => CreateLeaveType::route('/create'),
            'view'   => ViewLeaveType::route('/{record}'),
            'edit'   => EditLeaveType::route('/{record}/edit'),
        ];
    }
}
