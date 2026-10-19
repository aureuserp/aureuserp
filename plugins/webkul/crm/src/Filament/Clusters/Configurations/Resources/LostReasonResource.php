<?php

namespace Webkul\Crm\Filament\Clusters\Configurations\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Webkul\Crm\Filament\Clusters\Configurations;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\LostReasonResource\Pages\ManageLostReasons;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\LostReasonResource\Schemas\LostReasonForm;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\LostReasonResource\Tables\LostReasonsTable;
use Webkul\Crm\Models\LostReason;

class LostReasonResource extends Resource
{
    protected static ?string $model = LostReason::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-hand-thumb-down';

    protected static ?string $cluster = Configurations::class;

    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getModelLabel(): string
    {
        return __('crm::filament/clusters/configurations/resources/lost-reason.model-label');
    }

    public static function getNavigationLabel(): string
    {
        return __('crm::filament/clusters/configurations/resources/lost-reason.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('crm::filament/clusters/configurations/resources/lost-reason.navigation.group');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function form(Schema $schema): Schema
    {
        return LostReasonForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LostReasonsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageLostReasons::route('/'),
        ];
    }
}
