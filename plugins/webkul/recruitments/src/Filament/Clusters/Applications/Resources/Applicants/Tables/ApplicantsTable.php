<?php

namespace Webkul\Recruitment\Filament\Clusters\Applications\Resources\Applicants\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;
use Webkul\Recruitment\Enums\RecruitmentState as RecruitmentStateEnum;
use Webkul\Recruitment\Models\Applicant;

class ApplicantsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('candidate.partner.name')
                    ->label(__('recruitments::filament/clusters/applications/resources/applicant.table.columns.partner-name'))
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->searchable()
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('create_date')
                    ->date()
                    ->label(__('recruitments::filament/clusters/applications/resources/applicant.table.columns.applied-on'))
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->searchable()
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('job.name')
                    ->label(__('recruitments::filament/clusters/applications/resources/applicant.table.columns.job-position'))
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->placeholder('-')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('stage.name')
                    ->label(__('recruitments::filament/clusters/applications/resources/applicant.table.columns.stage'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder('-')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('candidate.name')
                    ->label(__('recruitments::filament/clusters/applications/resources/applicant.table.columns.candidate-name'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder('-')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('application_status')
                    ->label(__('recruitments::filament/clusters/applications/resources/applicant.table.columns.application-status'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->state(function (Applicant $record) {
                        return [
                            'label' => $record->application_status->getLabel(),
                            'color' => $record->application_status->getColor(),
                        ];
                    })
                    ->tooltip(fn ($record) => $record->refuseReason?->name)
                    ->formatStateUsing(function ($record) {
                        $html = '<span style="display: inline-flex; align-items: center; background-color: '.$record->application_status->getColor().'; color: white; padding: 4px 8px; border-radius: 12px; font-size: 18px; font-weight: 500;">';

                        $html .= view('filament::components.icon', [
                            'icon'  => $record->application_status->getIcon(),
                            'class' => 'w-6 h-6',
                        ])->render();

                        $html .= $record->application_status->getLabel();
                        $html .= '</span>';

                        return new HtmlString($html);
                    })
                    ->placeholder('-'),
                TextColumn::make('refuseReason.name')
                    ->label(__('recruitments::filament/clusters/applications/resources/applicant.table.columns.refuse-reason'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder('-')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('priority')
                    ->label(__('recruitments::filament/clusters/applications/resources/applicant.table.columns.evaluation'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(function ($state) {
                        $html = '<div class="flex gap-1" style="color: rgb(217 119 6);">';
                        for ($i = 1; $i <= 3; $i++) {
                            $iconType = $i <= $state ? 'heroicon-s-star' : 'heroicon-o-star';
                            $html .= view('filament::components.icon', [
                                'icon'  => $iconType,
                                'class' => 'w-5 h-5',
                            ])->render();
                        }

                        $html .= '</div>';

                        return new HtmlString($html);
                    })
                    ->placeholder('-')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('categories.name')
                    ->label(__('recruitments::filament/clusters/applications/resources/applicant.table.columns.tags'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->badge()
                    ->weight(FontWeight::Bold)
                    ->state(function (Applicant $record): array {
                        $tags = $record->categories ?? $record->candidate->categories;

                        return $tags->map(fn ($category) => [
                            'label' => $category->name,
                            'color' => $category->color ?? '#808080',
                        ])->toArray();
                    })
                    ->formatStateUsing(fn ($state) => $state['label'])
                    ->color(fn ($state) => Color::generateV3Palette($state['color'])),
                TextColumn::make('candidate.email_from')
                    ->label(__('recruitments::filament/clusters/applications/resources/applicant.table.columns.email'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('recruiter.name')
                    ->label(__('recruitments::filament/clusters/applications/resources/applicant.table.columns.recruiter'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('interviewer.name')
                    ->badge()
                    ->searchable()
                    ->sortable()
                    ->label(__('recruitments::filament/clusters/applications/resources/applicant.table.columns.interviewer'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('candidate.phone')
                    ->label(__('recruitments::filament/clusters/applications/resources/applicant.table.columns.candidate-phone'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('medium.name')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->label(__('recruitments::filament/clusters/applications/resources/applicant.table.columns.medium'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('source.name')
                    ->badge()
                    ->searchable()
                    ->sortable()
                    ->label(__('recruitments::filament/clusters/applications/resources/applicant.table.columns.source'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('salary_expected')
                    ->label(__('recruitments::filament/clusters/applications/resources/applicant.table.columns.salary-expected'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('candidate.availability_date')
                    ->label(__('recruitments::filament/clusters/applications/resources/applicant.table.columns.availability-date'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Tables\Grouping\Group::make('stage.name')
                    ->label(__('recruitments::filament/clusters/applications/resources/applicant.table.groups.stage'))
                    ->collapsible(),
                Tables\Grouping\Group::make('job.name')
                    ->label(__('recruitments::filament/clusters/applications/resources/applicant.table.groups.job-position'))
                    ->collapsible(),
                Tables\Grouping\Group::make('candidate.name')
                    ->label(__('recruitments::filament/clusters/applications/resources/applicant.table.groups.candidate-name'))
                    ->collapsible(),
                Tables\Grouping\Group::make('recruiter.name')
                    ->label(__('recruitments::filament/clusters/applications/resources/applicant.table.groups.responsible'))
                    ->collapsible(),
                Tables\Grouping\Group::make('created_at')
                    ->label(__('recruitments::filament/clusters/applications/resources/applicant.table.groups.creation-date'))
                    ->collapsible(),
                Tables\Grouping\Group::make('date_closed')
                    ->label(__('recruitments::filament/clusters/applications/resources/applicant.table.groups.hired-date'))
                    ->collapsible(),
                Tables\Grouping\Group::make('lastStage.name')
                    ->label(__('recruitments::filament/clusters/applications/resources/applicant.table.groups.last-stage'))
                    ->collapsible(),
                Tables\Grouping\Group::make('refuseReason.name')
                    ->label(__('Refuse Reason'))
                    ->label(__('recruitments::filament/clusters/applications/resources/applicant.table.groups.refuse-reason'))
                    ->collapsible(),
            ])
            ->filters([
                QueryBuilder::make()
                    ->constraintPickerColumns(5)
                    ->constraints([
                        RelationshipConstraint::make('source')
                            ->label(__('recruitments::filament/clusters/applications/resources/applicant.table.filters.source'))
                            ->icon('heroicon-o-building-office-2')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('medium')
                            ->label(__('recruitments::filament/clusters/applications/resources/applicant.table.filters.medium'))
                            ->icon('heroicon-o-link')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('candidate')
                            ->label(__('recruitments::filament/clusters/applications/resources/applicant.table.filters.candidate'))
                            ->icon('heroicon-o-user-circle')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('date_last_stage_updated')
                            ->label(__('recruitments::filament/clusters/applications/resources/applicant.table.filters.date-last-stage-updated'))
                            ->icon('heroicon-o-user-circle')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('stage')
                            ->label(__('recruitments::filament/clusters/applications/resources/applicant.table.filters.stage'))
                            ->icon('heroicon-o-user-circle')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('job')
                            ->label(__('recruitments::filament/clusters/applications/resources/applicant.table.filters.job-position'))
                            ->icon('heroicon-o-briefcase')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        TextConstraint::make('priority')
                            ->label(__('recruitments::filament/clusters/applications/resources/applicant.table.filters.priority'))
                            ->icon('heroicon-o-exclamation-circle'),
                        TextConstraint::make('salary_proposed_extra')
                            ->label(__('recruitments::filament/clusters/applications/resources/applicant.table.filters.salary-proposed-extra'))
                            ->icon('heroicon-o-currency-dollar'),
                        TextConstraint::make('salary_expected_extra')
                            ->label(__('recruitments::filament/clusters/applications/resources/applicant.table.filters.salary-expected-extra'))
                            ->icon('heroicon-o-currency-dollar'),
                        TextConstraint::make('applicant_notes')
                            ->label(__('recruitments::filament/clusters/applications/resources/applicant.table.filters.applicant-notes'))
                            ->icon('heroicon-o-document-text'),
                        DateConstraint::make('create_date')
                            ->label(__('recruitments::filament/clusters/applications/resources/applicant.table.filters.create-date'))
                            ->icon('heroicon-o-calendar'),
                        DateConstraint::make('date_closed')
                            ->label(__('recruitments::filament/clusters/applications/resources/applicant.table.filters.date-closed'))
                            ->icon('heroicon-o-check-badge'),
                    ]),
            ])
            ->defaultGroup('stage.name')
            ->columnToggleFormColumns(3)
            ->filtersFormColumns(2)
            ->filtersLayout(FiltersLayout::Dropdown)
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('recruitments::filament/clusters/applications/resources/applicant.table.actions.delete.notification.title'))
                                ->body(__('recruitments::filament/clusters/applications/resources/applicant.table.actions.delete.notification.body'))
                        ),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('recruitments::filament/clusters/applications/resources/applicant.table.bulk-actions.delete.notification.title'))
                                ->body(__('recruitments::filament/clusters/applications/resources/applicant.table.bulk-actions.delete.notification.body'))
                        ),
                    ForceDeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('recruitments::filament/clusters/applications/resources/applicant.table.bulk-actions.force-delete.notification.title'))
                                ->body(__('recruitments::filament/clusters/applications/resources/applicant.table.bulk-actions.force-delete.notification.body'))
                        ),
                    RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('recruitments::filament/clusters/applications/resources/applicant.table.bulk-actions.restore.notification.title'))
                                ->body(__('recruitments::filament/clusters/applications/resources/applicant.table.bulk-actions.restore.notification.body'))
                        ),
                ]),
            ])
            ->modifyQueryUsing(function (Builder $query) {
                $query->where('state', '!=', RecruitmentStateEnum::BLOCKED->value)
                    ->orWhereNull('state');
            });
    }
}
