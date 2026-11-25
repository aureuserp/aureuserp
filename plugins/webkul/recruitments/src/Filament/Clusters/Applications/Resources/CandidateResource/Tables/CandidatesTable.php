<?php

namespace Webkul\Recruitment\Filament\Clusters\Applications\Resources\CandidateResource\Tables;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\AvatarProviders\UiAvatarsProvider;
use Filament\Notifications\Notification;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Chatter\Filament\Actions\ActivityTableAction;
use Webkul\Recruitment\Models\Candidate;

class CandidatesTable
{
    public static function configure(Table $table, array $customColumns = [], array $customFilters = []): Table
    {
        return $table
            ->columns(array_merge([
                Stack::make([
                    Split::make([
                        ImageColumn::make('partner.avatar')
                            ->circular()
                            ->imageSize(64)
                            ->defaultImageUrl(fn (Candidate $record): string => (new UiAvatarsProvider)->get($record))
                            ->grow(false),

                        Stack::make([
                            TextColumn::make('name')
                                ->label(__('recruitments::filament/clusters/applications/resources/candidate.table.columns.name'))
                                ->weight(FontWeight::Bold)
                                ->size(TextSize::Large)
                                ->searchable()
                                ->sortable()
                                ->lineClamp(1)
                                ->tooltip(fn (Candidate $record): string => $record->name)
                                ->extraAttributes(['class' => 'fi-ta-card-title']),

                            Stack::make([
                                TextColumn::make('degree.name')
                                    ->label(__('recruitments::filament/clusters/applications/resources/candidate.table.columns.degree'))
                                    ->icon('heroicon-o-academic-cap')
                                    ->weight(FontWeight::Medium)
                                    ->tooltip(fn (Candidate $record): ?string => $record->degree?->name)
                                    ->lineClamp(1)
                                    ->extraAttributes(['class' => 'break-all']),
                            ])
                                ->visible(fn (Candidate $record): bool => filled($record->degree_id)),

                            Stack::make([
                                TextColumn::make('email_from')
                                    ->label(__('recruitments::filament/clusters/applications/resources/candidate.table.columns.email'))
                                    ->icon('heroicon-o-envelope')
                                    ->color('gray')
                                    ->searchable()
                                    ->sortable()
                                    ->tooltip(fn (Candidate $record): string => $record->email_from)
                                    ->lineClamp(1)
                                    ->extraAttributes(['class' => 'break-all']),
                            ])
                                ->visible(fn (Candidate $record): bool => filled($record->email_from)),

                            Stack::make([
                                TextColumn::make('phone')
                                    ->label(__('recruitments::filament/clusters/applications/resources/candidate.table.columns.phone'))
                                    ->icon('heroicon-o-phone')
                                    ->color('gray')
                                    ->searchable()
                                    ->sortable()
                                    ->tooltip(fn (Candidate $record): string => $record->phone)
                                    ->lineClamp(1)
                                    ->extraAttributes(['class' => 'break-all']),
                            ])
                                ->visible(fn (Candidate $record): bool => filled($record->phone)),

                            Stack::make([
                                TextColumn::make('manager.name')
                                    ->label(__('recruitments::filament/clusters/applications/resources/candidate.table.columns.manager'))
                                    ->icon('heroicon-o-user')
                                    ->color('gray')
                                    ->tooltip(fn (Candidate $record): ?string => $record->manager?->name)
                                    ->lineClamp(1)
                                    ->extraAttributes(['class' => 'break-all']),
                            ])
                                ->visible(fn (Candidate $record): bool => filled($record->manager_id)),
                        ])->space(1),
                    ])->from('sm'),
                ])->space(3),
            ], $customColumns))
            ->recordClasses('fi-ta-card-select')
            ->contentGrid([
                'sm'  => 1,
                'md'  => 2,
                'xl'  => 3,
                '2xl' => 4,
            ])
            ->paginated([
                12,
                24,
                36,
                'all',
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['partner', 'degree', 'manager', 'categories']))
            ->filtersFormColumns(2)
            ->filters(array_merge([
                QueryBuilder::make()
                    ->constraintPickerColumns(1)
                    ->constraints([
                        RelationshipConstraint::make('company')
                            ->label(__('recruitments::filament/clusters/applications/resources/candidate.table.filters.company'))
                            ->icon('heroicon-o-building-office-2')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('partner')
                            ->label(__('recruitments::filament/clusters/applications/resources/candidate.table.filters.partner-name'))
                            ->icon('heroicon-o-user')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('degree')
                            ->label(__('recruitments::filament/clusters/applications/resources/candidate.table.filters.degree'))
                            ->icon('heroicon-o-academic-cap')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('manager')
                            ->label(__('recruitments::filament/clusters/applications/resources/candidate.table.filters.manager-name'))
                            ->icon('heroicon-o-user')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                    ]),
            ], $customFilters))
            ->groups([
                Tables\Grouping\Group::make('manager.name')
                    ->label(__('recruitments::filament/clusters/applications/resources/candidate.table.groups.manager-name'))
                    ->collapsible(),
            ])
            ->recordActions([
                Action::make('evaluation')
                    ->label(__('recruitments::filament/clusters/applications/resources/candidate.table.columns.evaluation'))
                    ->view('recruitments::tables.actions.evaluation'),
                Action::make('tags')
                    ->label(__('recruitments::filament/clusters/applications/resources/candidate.table.columns.tags'))
                    ->view('support::tables.actions.tags', ['relationship' => 'categories'])
                    ->visible(fn (Candidate $record): bool => $record->categories->isNotEmpty()),
                ActivityTableAction::make()
                    ->extraAttributes(['class' => 'fi-ta-card-top-right']),
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('recruitments::filament/clusters/applications/resources/candidate.table.actions.delete.notification.title'))
                                ->body(__('recruitments::filament/clusters/applications/resources/candidate.table.actions.delete.notification.body'))
                        ),
                ])
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->color('gray')
                    ->extraDropdownAttributes(['class' => 'ms-auto']),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('recruitments::filament/clusters/applications/resources/candidate.table.bulk-actions.delete.notification.title'))
                                ->body(__('recruitments::filament/clusters/applications/resources/candidate.table.bulk-actions.delete.notification.body'))
                        ),
                ]),
            ])
            ->emptyStateActions([
                CreateAction::make()
                    ->icon('heroicon-o-plus-circle')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('recruitments::filament/clusters/applications/resources/candidate.table.empty-state-actions.create.notification.title'))
                            ->body(__('recruitments::filament/clusters/applications/resources/candidate.table.empty-state-actions.create.notification.body'))
                    ),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
