<?php

namespace Webkul\TimeOff\Filament\Clusters\Configurations\Resources;

use Filament\Resources\Pages\Page;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\TimeOff\Filament\Clusters\Configurations;
use Webkul\TimeOff\Filament\Clusters\Configurations\Resources\AccrualPlanResource\Pages\CreateAccrualPlan;
use Webkul\TimeOff\Filament\Clusters\Configurations\Resources\AccrualPlanResource\Pages\EditAccrualPlan;
use Webkul\TimeOff\Filament\Clusters\Configurations\Resources\AccrualPlanResource\Pages\ListAccrualPlans;
use Webkul\TimeOff\Filament\Clusters\Configurations\Resources\AccrualPlanResource\Pages\ManageMilestone;
use Webkul\TimeOff\Filament\Clusters\Configurations\Resources\AccrualPlanResource\Pages\ViewAccrualPlan;
use Webkul\TimeOff\Filament\Clusters\Configurations\Resources\AccrualPlanResource\RelationManagers\MilestoneRelationManager;
use Webkul\TimeOff\Filament\Clusters\Configurations\Resources\AccrualPlanResource\Schemas\AccrualPlanForm;
use Webkul\TimeOff\Filament\Clusters\Configurations\Resources\AccrualPlanResource\Schemas\AccrualPlanInfolist;
use Webkul\TimeOff\Filament\Clusters\Configurations\Resources\AccrualPlanResource\Tables\AccrualPlansTable;
use Webkul\TimeOff\Models\LeaveAccrualPlan;

class AccrualPlanResource extends Resource
{
    protected static ?string $model = LeaveAccrualPlan::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-paper-airplane';

    protected static ?string $cluster = Configurations::class;

    protected static ?int $navigationSort = 2;

    public static function getModelLabel(): string
    {
        return __('time-off::filament/clusters/configurations/resources/accrual-plan.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('time-off::filament/clusters/configurations/resources/accrual-plan.navigation.title');
    }

    public static function form(Schema $schema): Schema
    {
        return AccrualPlanForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AccrualPlansTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AccrualPlanInfolist::configure($schema);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewAccrualPlan::class,
            EditAccrualPlan::class,
            ManageMilestone::class,
        ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationGroup::make('Manage Milestones', [
                MilestoneRelationManager::class,
            ])
                ->icon('heroicon-o-clipboard-list'),
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'      => ListAccrualPlans::route('/'),
            'create'     => CreateAccrualPlan::route('/create'),
            'view'       => ViewAccrualPlan::route('/{record}'),
            'edit'       => EditAccrualPlan::route('/{record}/edit'),
            'milestones' => ManageMilestone::route('/{record}/milestones'),
        ];
    }
}
