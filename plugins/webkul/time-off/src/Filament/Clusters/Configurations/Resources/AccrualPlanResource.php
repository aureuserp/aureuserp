<?php

namespace Webkul\TimeOff\Filament\Clusters\Configurations\Resources;

use Carbon\Carbon;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Webkul\TimeOff\Enums\AccruedGainTime;
use Webkul\TimeOff\Enums\CarryoverDate;
use Webkul\TimeOff\Enums\CarryoverMonth;
use Webkul\TimeOff\Filament\Clusters\Configurations;
use Webkul\TimeOff\Filament\Clusters\Configurations\Resources\AccrualPlanResource\Pages\CreateAccrualPlan;
use Webkul\TimeOff\Filament\Clusters\Configurations\Resources\AccrualPlanResource\Pages\EditAccrualPlan;
use Webkul\TimeOff\Filament\Clusters\Configurations\Resources\AccrualPlanResource\Pages\ListAccrualPlans;
use Webkul\TimeOff\Filament\Clusters\Configurations\Resources\AccrualPlanResource\Pages\ManageMilestone;
use Webkul\TimeOff\Filament\Clusters\Configurations\Resources\AccrualPlanResource\Pages\ViewAccrualPlan;
use Webkul\TimeOff\Filament\Clusters\Configurations\Resources\AccrualPlanResource\RelationManagers\MilestoneRelationManager;
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
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Group::make()
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('Name'))
                                    ->label(__('time-off::filament/clusters/configurations/resources/accrual-plan.form.fields.name'))
                                    ->required(),
                                Toggle::make('is_based_on_worked_time')
                                    ->inline(false)
                                    ->label(__('Is Based On Worked Time'))
                                    ->label(__('time-off::filament/clusters/configurations/resources/accrual-plan.form.fields.is-based-on-worked-time')),
                                Radio::make('accrued_gain_time')
                                    ->label(__('Accrued Gain Time'))
                                    ->label(__('time-off::filament/clusters/configurations/resources/accrual-plan.form.fields.accrued-gain-time'))
                                    ->options(AccruedGainTime::class)
                                    ->default(AccruedGainTime::END->value)
                                    ->required(),
                                Radio::make('carryover_date')
                                    ->label(__('Carry-Over Time'))
                                    ->label(__('time-off::filament/clusters/configurations/resources/accrual-plan.form.fields.carry-over-time'))
                                    ->options(CarryoverDate::class)
                                    ->default(CarryoverDate::OTHER->value)
                                    ->live()
                                    ->required(),
                                Fieldset::make()
                                    ->label('Carry-Over Date')
                                    ->label(__('time-off::filament/clusters/configurations/resources/accrual-plan.form.fields.carry-over-date'))
                                    ->live()
                                    ->visible(function (Get $get) {
                                        return $get('carryover_date') === CarryoverDate::OTHER->value;
                                    })
                                    ->schema([
                                        Select::make('carryover_day')
                                            ->hiddenLabel()
                                            ->options(function (Get $get) {
                                                $monthValue = $get('carryover_month') ?? CarryoverMonth::JAN->value;

                                                $monthEnum = CarryoverMonth::from($monthValue);

                                                $monthNumber = $monthEnum->toNumber();
                                                $year = now()->year;

                                                $daysInMonth = Carbon::createFromDate($year, $monthNumber, 1)->daysInMonth;

                                                return collect(range(1, $daysInMonth))
                                                    ->mapWithKeys(fn ($day) => [$day => $day])
                                                    ->toArray();
                                            })

                                            ->maxWidth(Width::ExtraSmall)
                                            ->default(1)
                                            ->required(),
                                        Select::make('carryover_month')
                                            ->hiddenLabel()
                                            ->options(CarryoverMonth::class)
                                            ->reactive()
                                            ->default(CarryoverMonth::JAN->value)
                                            ->required(),
                                    ])->columns(2),
                                Toggle::make('is_active')
                                    ->inline(false)
                                    ->label(__('Status'))
                                    ->label(__('time-off::filament/clusters/configurations/resources/accrual-plan.form.fields.status')),
                            ]),
                    ])->columns(2)->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->label(__('time-off::filament/clusters/configurations/resources/accrual-plan.table.columns.name')),
                TextColumn::make('leaveAccrualLevels')
                    ->formatStateUsing(fn ($record) => $record?->leaveAccrualLevels?->count() ?? 0)
                    ->label(__('time-off::filament/clusters/configurations/resources/accrual-plan.table.columns.levels')),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->title(__('time-off::filament/clusters/configurations/resources/accrual-plan.table.actions.delete.notification.title'))
                            ->body(__('time-off::filament/clusters/configurations/resources/accrual-plan.table.actions.delete.notification.body'))
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->title(__('time-off::filament/clusters/configurations/resources/accrual-plan.table.bulk-actions.delete.notification.title'))
                                ->body(__('time-off::filament/clusters/configurations/resources/accrual-plan.table.bulk-actions.delete.notification.body'))
                        ),
                ]),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(['default' => 2])
                    ->schema([
                        Group::make()
                            ->schema([
                                Section::make(__('Basic Information'))
                                    ->schema([
                                        TextEntry::make('name')
                                            ->icon('heroicon-o-user')
                                            ->placeholder('—')
                                            ->label(__('time-off::filament/clusters/configurations/resources/accrual-plan.infolist.entries.name')),
                                        IconEntry::make('is_based_on_worked_time')
                                            ->boolean()
                                            ->label(__('time-off::filament/clusters/configurations/resources/accrual-plan.infolist.entries.is-based-on-worked-time')),
                                        TextEntry::make('accrued_gain_time')
                                            ->icon('heroicon-o-clock')
                                            ->placeholder('—')
                                            ->label(__('time-off::filament/clusters/configurations/resources/accrual-plan.infolist.entries.accrued-gain-time')),
                                        TextEntry::make('carryover_date')
                                            ->icon('heroicon-o-calendar')
                                            ->placeholder('—')
                                            ->label(__('time-off::filament/clusters/configurations/resources/accrual-plan.infolist.entries.carry-over-time')),
                                        TextEntry::make('carryover_day')
                                            ->icon('heroicon-o-calendar')
                                            ->placeholder('—')
                                            ->label(__('Carryover Day'))
                                            ->label(__('time-off::filament/clusters/configurations/resources/accrual-plan.infolist.entries.carry-over-day')),
                                        TextEntry::make('carryover_month')
                                            ->icon('heroicon-o-calendar')
                                            ->placeholder('—')
                                            ->label(__('Carryover Month'))
                                            ->label(__('time-off::filament/clusters/configurations/resources/accrual-plan.infolist.entries.carry-over-month')),
                                    ]),
                            ])
                            ->columnSpan(2),
                    ])->columnSpanFull(),
            ]);
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
