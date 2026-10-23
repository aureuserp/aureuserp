<?php

namespace Webkul\Crm\Filament\Clusters\Configurations\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Webkul\Crm\Filament\Clusters\Configurations;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\RecurringPlanResource\Pages\ManageRecurringPlans;
use Webkul\Crm\Models\RecurringPlan;
use Webkul\Crm\Settings\CrmSettings;

class RecurringPlanResource extends Resource
{
    protected static ?string $model = RecurringPlan::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-path-rounded-square';

    protected static ?string $cluster = Configurations::class;

    protected static ?int $navigationSort = 5;

    protected static ?string $recordTitleAttribute = 'name';

    public static function isRecurringRevenueEnabled(): bool
    {
        return (bool) app(CrmSettings::class)->use_recurring_revenues;
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

    public static function getNavigationGroup(): string
    {
        return __('crm::filament/clusters/configurations/resources/lost-reason.navigation.group');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([SoftDeletingScope::class]);
    }

    public static function form(Schema $schema): Schema
    {
        $label = fn (string $key): string => __('crm::filament/clusters/configurations/resources/recurring-plan.form.'.$key);

        return $schema
            ->components([
                TextInput::make('name')
                    ->label($label('name'))
                    ->placeholder($label('name-placeholder'))
                    ->required()
                    ->maxLength(255),
                TextInput::make('number_of_months')
                    ->label($label('months'))
                    ->helperText($label('months-helper'))
                    ->numeric()
                    ->integer()
                    ->minValue(1)
                    ->default(1)
                    ->required(),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        $label = fn (string $key): string => __('crm::filament/clusters/configurations/resources/recurring-plan.'.$key);

        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->withCount('leads'))
            ->columns([
                TextColumn::make('name')
                    ->label($label('table.columns.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('number_of_months')
                    ->label($label('table.columns.months'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('leads_count')
                    ->label($label('table.columns.opportunities'))
                    ->numeric(),
            ])
            ->defaultSort('sort')
            ->reorderable('sort')
            ->recordActions([
                EditAction::make()
                    ->hidden(fn (RecurringPlan $record): bool => $record->trashed())
                    ->successNotification(Notification::make()->success()->title($label('notifications.updated'))),
                RestoreAction::make()
                    ->successNotification(Notification::make()->success()->title($label('notifications.restored'))),
                DeleteAction::make()
                    ->label($label('actions.archive'))
                    ->icon('heroicon-o-archive-box')
                    ->modalHeading($label('actions.archive-heading'))
                    ->modalDescription($label('actions.archive-description'))
                    ->modalSubmitActionLabel($label('actions.archive'))
                    ->successNotification(Notification::make()->success()->title($label('notifications.archived'))),
                ForceDeleteAction::make()
                    ->successNotification(Notification::make()->success()->title($label('notifications.deleted'))),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    RestoreBulkAction::make(),
                    DeleteBulkAction::make()
                        ->label($label('actions.archive'))
                        ->icon('heroicon-o-archive-box')
                        ->modalHeading($label('actions.archive-heading'))
                        ->modalDescription($label('actions.archive-description'))
                        ->modalSubmitActionLabel($label('actions.archive')),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageRecurringPlans::route('/'),
        ];
    }
}
