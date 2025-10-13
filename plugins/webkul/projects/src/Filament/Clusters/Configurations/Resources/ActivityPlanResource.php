<?php

namespace Webkul\Project\Filament\Clusters\Configurations\Resources;

use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Webkul\Project\Filament\Clusters\Configurations;
use Webkul\Project\Filament\Clusters\Configurations\Resources\ActivityPlanResource\Pages\EditActivityPlan;
use Webkul\Project\Filament\Clusters\Configurations\Resources\ActivityPlanResource\Pages\ListActivityPlans;
use Webkul\Project\Filament\Clusters\Configurations\Resources\ActivityPlanResource\Pages\ViewActivityPlan;
use Webkul\Project\Filament\Clusters\Configurations\Resources\ActivityPlanResource\RelationManagers\ActivityTemplateRelationManager;
use Webkul\Support\Models\ActivityPlan;

class ActivityPlanResource extends Resource
{
    protected static ?string $model = ActivityPlan::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-briefcase';

    protected static ?int $navigationSort = 5;

    protected static ?string $cluster = Configurations::class;

    public static function getNavigationLabel(): string
    {
        return __('projects::filament/clusters/configurations/resources/activity-plan.navigation.title');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        TextInput::make('name')
                            ->label(__('projects::filament/clusters/configurations/resources/activity-plan.form.name'))
                            ->required()
                            ->maxLength(255),
                        Toggle::make('is_active')
                            ->label(__('projects::filament/clusters/configurations/resources/activity-plan.form.status'))
                            ->default(true)
                            ->inline(false),
                    ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('projects::filament/clusters/configurations/resources/activity-plan.table.columns.name'))
                    ->searchable()
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label(__('projects::filament/clusters/configurations/resources/activity-plan.table.columns.status'))
                    ->sortable()
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label(__('projects::filament/clusters/configurations/resources/activity-plan.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('projects::filament/clusters/configurations/resources/activity-plan.table.columns.updated-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Group::make('name')
                    ->label(__('projects::filament/clusters/configurations/resources/activity-plan.table.groups.name'))
                    ->collapsible(),
                Group::make('is_active')
                    ->label(__('projects::filament/clusters/configurations/resources/activity-plan.table.groups.status'))
                    ->collapsible(),
                Group::make('created_at')
                    ->label(__('projects::filament/clusters/configurations/resources/activity-plan.table.groups.created-at'))
                    ->collapsible(),
                Group::make('updated_at')
                    ->label(__('projects::filament/clusters/configurations/resources/activity-plan.table.groups.updated-at'))
                    ->date()
                    ->collapsible(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->hidden(fn ($record) => $record->trashed()),
                EditAction::make()
                    ->hidden(fn ($record) => $record->trashed()),
                RestoreAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('projects::filament/clusters/configurations/resources/activity-plan.table.actions.restore.notification.title'))
                            ->body(__('projects::filament/clusters/configurations/resources/activity-plan.table.actions.restore.notification.body')),
                    ),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('projects::filament/clusters/configurations/resources/activity-plan.table.actions.delete.notification.title'))
                            ->body(__('projects::filament/clusters/configurations/resources/activity-plan.table.actions.delete.notification.body')),
                    ),
                ForceDeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('projects::filament/clusters/configurations/resources/activity-plan.table.actions.force-delete.notification.title'))
                            ->body(__('projects::filament/clusters/configurations/resources/activity-plan.table.actions.force-delete.notification.body')),
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('projects::filament/clusters/configurations/resources/activity-plan.table.bulk-actions.restore.notification.title'))
                                ->body(__('projects::filament/clusters/configurations/resources/activity-plan.table.bulk-actions.restore.notification.body')),
                        ),
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('projects::filament/clusters/configurations/resources/activity-plan.table.bulk-actions.delete.notification.title'))
                                ->body(__('projects::filament/clusters/configurations/resources/activity-plan.table.bulk-actions.delete.notification.body')),
                        ),
                    ForceDeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('projects::filament/clusters/configurations/resources/activity-plan.table.bulk-actions.force-delete.notification.title'))
                                ->body(__('projects::filament/clusters/configurations/resources/activity-plan.table.bulk-actions.force-delete.notification.body')),
                        ),
                ]),
            ])
            ->emptyStateActions([
                CreateAction::make()
                    ->icon('heroicon-o-plus-circle')
                    ->mutateDataUsing(function (array $data): array {
                        $user = Auth::user();

                        $data['plugin'] = 'projects';

                        $data['creator_id'] = $user->id;

                        $data['company_id'] ??= $user->defaultCompany?->id;

                        return $data;
                    })
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('projects::filament/clusters/configurations/resources/activity-plan.table.empty-state.create.notification.title'))
                            ->body(__('projects::filament/clusters/configurations/resources/activity-plan.table.empty-state.create.notification.body')),
                    ),
            ])
            ->modifyQueryUsing(function ($query) {
                $query->where('plugin', 'projects');
            });
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('General Information')
                    ->schema([
                        TextEntry::make('name')
                            ->label(__('projects::filament/clusters/configurations/resources/activity-plan.infolist.name'))
                            ->icon('heroicon-o-briefcase')
                            ->placeholder('—'),
                        IconEntry::make('is_active')
                            ->label(__('projects::filament/clusters/configurations/resources/activity-plan.infolist.status'))
                            ->boolean(),
                    ])
                    ->columns(2)->columnSpanFull(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ActivityTemplateRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListActivityPlans::route('/'),
            'view'   => ViewActivityPlan::route('/{record}'),
            'edit'   => EditActivityPlan::route('/{record}/edit'),
        ];
    }
}
