<?php

namespace Webkul\Recruitment\Filament\Clusters\Configurations\Resources\RefuseReasons;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Recruitment\Filament\Clusters\Configurations;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\RefuseReasons\Pages\ListRefuseReasons;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\RefuseReasons\Schemas\RefuseReasonForm;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\RefuseReasons\Schemas\RefuseReasonInfolist;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\RefuseReasons\Tables\RefuseReasonsTable;
use Webkul\Recruitment\Models\RefuseReason;

class RefuseReasonResource extends Resource
{
    protected static ?string $model = RefuseReason::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrow-uturn-left';

    protected static ?string $cluster = Configurations::class;

    public static function getModelLabel(): string
    {
        return __('recruitments::filament/clusters/configurations/resources/refuse-reason.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('recruitments::filament/clusters/configurations/resources/refuse-reason.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('recruitments::filament/clusters/configurations/resources/refuse-reason.navigation.title');
    }

    public static function form(Schema $schema): Schema
    {
        return RefuseReasonForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RefuseReasonsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return RefuseReasonInfolist::configure($schema);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRefuseReasons::route('/'),
        ];
    }
}
