<?php

namespace Webkul\TimeOff\Filament\Clusters\Configurations\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Resources\Resource;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Table;
use Webkul\TimeOff\Enums\AccruedGainTime;
use Webkul\TimeOff\Enums\CarryoverDate;
use Webkul\TimeOff\Enums\CarryoverDay;
use Webkul\TimeOff\Enums\CarryoverMonth;
use Webkul\TimeOff\Filament\Clusters\Configurations;
use Webkul\TimeOff\Filament\Clusters\Configurations\Resources\AccrualPlanResource\Pages;
use Webkul\TimeOff\Filament\Clusters\Configurations\Resources\AccrualPlanResource\RelationManagers;
use Webkul\TimeOff\Models\LeaveAccrualPlan;

class AccrualPlanResource extends Resource
{
    protected static ?string $model = LeaveAccrualPlan::class;

    protected static ?string $navigationIcon = 'heroicon-o-paper-airplane';

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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Group::make()
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label(__('Name'))
                                    ->label(__('time-off::filament/clusters/configurations/resources/accrual-plan.form.fields.name'))
                                    ->required(),
                                Forms\Components\Toggle::make('is_based_on_worked_time')
                                    ->inline(false)
                                    ->label(__('Is Based On Worked Time'))
                                    ->label(__('time-off::filament/clusters/configurations/resources/accrual-plan.form.fields.is-based-on-worked-time')),
                                Forms\Components\Radio::make('accrued_gain_time')
                                    ->label(__('Accrued Gain Time'))
                                    ->label(__('time-off::filament/clusters/configurations/resources/accrual-plan.form.fields.accrued-gain-time'))
                                    ->options(AccruedGainTime::class)
                                    ->default(AccruedGainTime::END->value)
                                    ->required(),
                                Forms\Components\Radio::make('carryover_date')
                                    ->label(__('Carry-Over Time'))
                                    ->label(__('time-off::filament/clusters/configurations/resources/accrual-plan.form.fields.carry-over-time'))
                                    ->options(CarryoverDate::class)
                                    ->default(CarryoverDate::OTHER->value)
                                    ->live()
                                    ->required(),
                                Forms\Components\Fieldset::make()
                                    ->label('Carry-Over Date')
                                    ->label(__('time-off::filament/clusters/configurations/resources/accrual-plan.form.fields.carry-over-date'))
                                    ->live()
                                    ->visible(function (Get $get) {
                                        return $get('carryover_date') === CarryoverDate::OTHER->value;
                                    })
                                    ->schema([
                                        Forms\Components\Select::make('carryover_day')
                                            ->hiddenLabel()
                                            ->options(CarryoverDay::class)
                                            ->maxWidth(MaxWidth::ExtraSmall)
                                            ->default(CarryoverDay::DAY_1->value)
                                            ->required(),
                                        Forms\Components\Select::make('carryover_month')
                                            ->hiddenLabel()
                                            ->options(CarryoverMonth::class)
                                            ->default(CarryoverMonth::JAN->value)
                                            ->required(),
                                    ])->columns(2),
                                Forms\Components\Toggle::make('is_active')
                                    ->inline(false)
                                    ->label(__('Status'))
                                    ->label(__('time-off::filament/clusters/configurations/resources/accrual-plan.form.fields.status')),
                            ]),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->label(__('time-off::filament/clusters/configurations/resources/accrual-plan.table.columns.name')),
                Tables\Columns\TextColumn::make('leaveAccrualLevels')
                    ->formatStateUsing(fn ($record) => $record?->leaveAccrualLevels?->count() ?? 0)
                    ->label(__('time-off::filament/clusters/configurations/resources/accrual-plan.table.columns.levels')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->title(__('time-off::filament/clusters/configurations/resources/accrual-plan.table.actions.delete.notification.title'))
                            ->body(__('time-off::filament/clusters/configurations/resources/accrual-plan.table.actions.delete.notification.body'))
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->title(__('time-off::filament/clusters/configurations/resources/accrual-plan.table.bulk-actions.delete.notification.title'))
                                ->body(__('time-off::filament/clusters/configurations/resources/accrual-plan.table.bulk-actions.delete.notification.body'))
                        ),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Grid::make(['default' => 2])
                    ->schema([
                        Infolists\Components\Group::make()
                            ->schema([
                                Infolists\Components\Section::make(__('Basic Information'))
                                    ->schema([
                                        Infolists\Components\TextEntry::make('name')
                                            ->icon('heroicon-o-user')
                                            ->placeholder('—')
                                            ->label(__('time-off::filament/clusters/configurations/resources/accrual-plan.infolist.entries.name')),
                                        Infolists\Components\IconEntry::make('is_based_on_worked_time')
                                            ->boolean()
                                            ->label(__('time-off::filament/clusters/configurations/resources/accrual-plan.infolist.entries.is-based-on-worked-time')),
                                        Infolists\Components\TextEntry::make('accrued_gain_time')
                                            ->icon('heroicon-o-clock')
                                            ->placeholder('—')
                                            ->label(__('time-off::filament/clusters/configurations/resources/accrual-plan.infolist.entries.accrued-gain-time')),
                                        Infolists\Components\TextEntry::make('carryover_date')
                                            ->icon('heroicon-o-calendar')
                                            ->placeholder('—')
                                            ->label(__('time-off::filament/clusters/configurations/resources/accrual-plan.infolist.entries.carry-over-time')),
                                        Infolists\Components\TextEntry::make('carryover_day')
                                            ->icon('heroicon-o-calendar')
                                            ->placeholder('—')
                                            ->label(__('Carryover Day'))
                                            ->label(__('time-off::filament/clusters/configurations/resources/accrual-plan.infolist.entries.carry-over-day')),
                                        Infolists\Components\TextEntry::make('carryover_month')
                                            ->icon('heroicon-o-calendar')
                                            ->placeholder('—')
                                            ->label(__('Carryover Month'))
                                            ->label(__('time-off::filament/clusters/configurations/resources/accrual-plan.infolist.entries.carry-over-month')),
                                    ]),
                            ])
                            ->columnSpan(2),
                    ]),
            ]);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewAccrualPlan::class,
            Pages\EditAccrualPlan::class,
            Pages\ManageMilestone::class,
        ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationGroup::make('Manage Milestones', [
                RelationManagers\MilestoneRelationManager::class,
            ])
                ->icon('heroicon-o-clipboard-list'),
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'      => Pages\ListAccrualPlans::route('/'),
            'create'     => Pages\CreateAccrualPlan::route('/create'),
            'view'       => Pages\ViewAccrualPlan::route('/{record}'),
            'edit'       => Pages\EditAccrualPlan::route('/{record}/edit'),
            'milestones' => Pages\ManageMilestone::route('/{record}/milestones'),
        ];
    }
}
