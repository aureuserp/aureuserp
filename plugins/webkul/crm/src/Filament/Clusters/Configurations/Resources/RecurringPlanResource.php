<?php

namespace Webkul\Crm\Filament\Clusters\Configurations\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Webkul\Crm\Filament\Clusters\Configurations;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\RecurringPlanResource\Pages\ManageRecurringPlans;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\RecurringPlanResource\Schemas\RecurringPlanForm;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\RecurringPlanResource\Tables\RecurringPlansTable;
use Webkul\Crm\Models\RecurringPlan;
use Webkul\Crm\Settings\CRMSettings;

class RecurringPlanResource extends Resource
{
    protected static ?string $model = RecurringPlan::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-path-rounded-square';

    protected static ?string $cluster = Configurations::class;

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';

    public static function isRecurringRevenueEnabled(): bool
    {
        return (bool) app(CRMSettings::class)->use_recurring_revenues;
    }

    public static function canViewAny(): bool
    {
        return static::isRecurringRevenueEnabled() && parent::canViewAny();
    }

    public static function getModelLabel(): string
    {
        return __('crm::filament/clusters/configurations/resources/recurring-plan.model-label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('crm::filament/clusters/configurations/resources/recurring-plan.plural-model-label');
    }

    public static function getNavigationLabel(): string
    {
        return __('crm::filament/clusters/configurations/resources/recurring-plan.navigation.title');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([SoftDeletingScope::class]);
    }

    public static function form(Schema $schema): Schema
    {
        return RecurringPlanForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RecurringPlansTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageRecurringPlans::route('/'),
        ];
    }
}
