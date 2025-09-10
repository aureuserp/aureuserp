<?php

namespace Webkul\Recruitment\Filament\Clusters\Applications\Resources\Candidates\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Webkul\Recruitment\Models\Candidate;

class CandidatesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Stack::make([
                    Stack::make([
                        TextColumn::make('name')
                            ->weight(FontWeight::Bold)
                            ->label(__('recruitments::filament/clusters/applications/resources/candidate.table.columns.name'))
                            ->searchable()
                            ->sortable(),
                        Stack::make([
                            TextColumn::make('categories.name')
                                ->label(__('recruitments::filament/clusters/applications/resources/candidate.table.columns.tags'))
                                ->badge()
                                ->searchable()
                                ->weight(FontWeight::Bold)
                                ->state(function (Candidate $record): array {
                                    return $record->categories->map(fn ($category) => [
                                        'label' => $category->name,
                                        'color' => $category->color ?? '#808080',
                                    ])->toArray();
                                })
                                ->formatStateUsing(fn ($state) => $state['label'])
                                ->color(fn ($state) => Color::generateV3Palette($state['color'])),
                            TextColumn::make('priority')
                                ->label(__('recruitments::filament/clusters/applications/resources/candidate.table.columns.evaluation'))
                                ->color('warning')
                                ->formatStateUsing(function ($state) {
                                    $html = '<div class="flex gap-1" style="margin-top: 6px;">';
                                    for ($i = 1; $i <= 3; $i++) {
                                        $iconType = $i <= $state ? 'heroicon-s-star' : 'heroicon-o-star';
                                        $html .= view('filament::components.icon', [
                                            'icon'  => $iconType,
                                            'class' => 'w-5 h-5',
                                        ])->render();
                                    }
                                    $html .= '</div>';

                                    return new HtmlString($html);
                                }),
                        ])
                            ->visible(fn ($record) => filled($record?->categories?->count())),
                    ])->space(1),
                ])
                    ->space(4),
            ])
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ])
            ->filters([
                QueryBuilder::make()
                    ->constraintPickerColumns(5)
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
                        TextConstraint::make('manager')
                            ->label(__('recruitments::filament/clusters/applications/resources/candidate.table.filters.manager-name'))
                            ->icon('heroicon-o-user'),
                    ]),
            ])
            ->groups([
                Tables\Grouping\Group::make('manager.name')
                    ->label(__('recruitments::filament/clusters/applications/resources/candidate.table.groups.manager-name'))
                    ->collapsible(),
            ])
            ->recordActions([
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
