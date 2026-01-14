<?php

namespace Webkul\Recruitment\Filament\Clusters\Applications\Resources\ApplicantResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Webkul\Recruitment\Enums\RecruitmentState;
use Webkul\Recruitment\Filament\Clusters\Applications\Resources\ApplicantResource;
use Webkul\Recruitment\Filament\Clusters\Applications\Resources\CandidateResource;
use Webkul\Recruitment\Filament\Widgets\JobPositionStatsWidget;
use Webkul\TableViews\Filament\Components\PresetView;
use Webkul\TableViews\Filament\Concerns\HasTableViews;

class ListApplicants extends ListRecords
{
    use HasTableViews;

    protected static string $resource = ApplicantResource::class;

    public function getHeaderWidgets(): array
    {
        return [
            JobPositionStatsWidget::make(),
        ];
    }

    public function getPresetTableViews(): array
    {
        return [
            'my_applications' => PresetView::make(__('recruitments::filament/clusters/applications/resources/applicant/pages/list-applicant.tabs.my-applicants'))
                ->icon('heroicon-s-user-circle')
                ->favorite()
                ->modifyQueryUsing(function (Builder $query) {
                    return $query
                        ->where('recruiter_id', Auth::id());
                }),

            'un_assigned' => PresetView::make(__('recruitments::filament/clusters/applications/resources/applicant/pages/list-applicant.tabs.un-assigned'))
                ->icon('heroicon-s-user-minus')
                ->favorite()
                ->modifyQueryUsing(function (Builder $query) {
                    return $query
                        ->whereNull('recruiter_id');
                }),

            'in_progress' => PresetView::make(__('recruitments::filament/clusters/applications/resources/applicant/pages/list-applicant.tabs.in-progress'))
                ->icon('heroicon-s-arrow-path')
                ->favorite()
                ->modifyQueryUsing(function (Builder $query) {
                    return $query
                        ->whereNull('deleted_at')
                        ->where('is_active', true)
                        ->whereNull('refuse_reason_id')
                        ->whereNull('date_closed');
                }),

            'hired' => PresetView::make(__('recruitments::filament/clusters/applications/resources/applicant/pages/list-applicant.tabs.hired'))
                ->icon('heroicon-s-check-badge')
                ->modifyQueryUsing(function (Builder $query) {
                    return $query
                        ->whereNull('deleted_at')
                        ->where('is_active', true)
                        ->whereNotNull('date_closed');
                }),

            'refused' => PresetView::make(__('recruitments::filament/clusters/applications/resources/applicant/pages/list-applicant.tabs.refused'))
                ->icon('heroicon-s-no-symbol')
                ->modifyQueryUsing(function (Builder $query) {
                    return $query
                        ->whereNull('deleted_at')
                        ->where('is_active', true)
                        ->whereNotNull('refuse_reason_id');
                }),

            'archived' => PresetView::make(__('recruitments::filament/clusters/applications/resources/applicant/pages/list-applicant.tabs.archived'))
                ->icon('heroicon-s-archive-box')
                ->favorite()
                ->modifyQueryUsing(function (Builder $query) {
                    return $query
                        ->withoutGlobalScope(SoftDeletingScope::class)
                        ->where(function (Builder $subQuery) {
                            $subQuery
                                ->whereNotNull('deleted_at')
                                ->orWhere('is_active', false);
                        });
                }),

            'blocked' => PresetView::make(__('recruitments::filament/clusters/applications/resources/applicant/pages/list-applicant.tabs.blocked'))
                ->icon('heroicon-s-shield-exclamation')
                ->modifyQueryUsing(function (Builder $query) {
                    return $query
                        ->where(function (Builder $subQuery) {
                            $subQuery
                                ->where('is_active', false)
                                ->orWhere('state', RecruitmentState::BLOCKED->value);
                        });
                }),

            'directly_available' => PresetView::make(__('recruitments::filament/clusters/applications/resources/applicant/pages/list-applicant.tabs.directly-available'))
                ->icon('heroicon-s-clock')
                ->modifyQueryUsing(function (Builder $query) {
                    return $query
                        ->join('recruitments_candidates as candidates', 'recruitments_applicants.candidate_id', '=', 'candidates.id')
                        ->where(function (Builder $subQuery) {
                            $subQuery
                                ->where('candidates.availability_date', '<=', now()->format('Y-m-d'))
                                ->orWhere('candidates.availability_date', false);
                        });
                }),

            'created_recently' => PresetView::make(__('recruitments::filament/clusters/applications/resources/applicant/pages/list-applicant.tabs.created-recently'))
                ->icon('heroicon-s-sparkles')
                ->modifyQueryUsing(function (Builder $query) {
                    return $query->where('create_date', '>=', now()->subDays(30)->toDateString());
                }),

            'stage_updated_recently' => PresetView::make(__('recruitments::filament/clusters/applications/resources/applicant/pages/list-applicant.tabs.stage-updated-recently'))
                ->icon('heroicon-s-arrows-pointing-out')
                ->modifyQueryUsing(function (Builder $query) {
                    return $query->where('date_last_stage_updated', '>=', now()->subDays(30));
                }),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->icon('heroicon-o-plus-circle')
                ->label(__('recruitments::filament/clusters/applications/resources/applicant/pages/list-applicant.header-actions.create-applicant.label'))
                ->modalHeading(__('recruitments::filament/clusters/applications/resources/applicant/pages/list-applicant.header-actions.create-applicant.modal-title'))
                ->modalIcon('heroicon-s-user')
                ->schema([
                    Group::make()
                        ->schema([
                            Select::make('candidate_id')
                                ->relationship('candidate', 'name')
                                ->required()
                                ->searchable()
                                ->preload()
                                ->label('Candidate')
                                ->createOptionForm(fn (Schema $schema) => CandidateResource::form($schema))
                                ->createOptionAction(function (Action $action) {
                                    return $action
                                        ->modalIcon('heroicon-s-user-plus')
                                        ->modalWidth(Width::SixExtraLarge);
                                }),
                        ])->columns(2),
                ])
                ->mutateDataUsing(function (array $data): array {
                    $data['creator_id'] = Auth::id();
                    $data['company_id'] = Auth::user()->default_company_id;
                    $data['create_date'] = now();
                    $data['is_active'] = true;

                    return $data;
                })
                ->using(function (array $data): \Illuminate\Database\Eloquent\Model {
                    $model = static::$resource::getModel();

                    $existingApplicant = $model::where('candidate_id', $data['candidate_id'])
                        ->where('company_id', $data['company_id'])
                        ->first();

                    if ($existingApplicant) {
                        $existingApplicant->update([
                            'is_active'        => true,
                            'deleted_at'       => null,
                            'refuse_reason_id' => null,
                            'date_closed'      => null,
                            'recruiter_id'     => $data['creator_id'],
                        ]);

                        return $existingApplicant;
                    }

                    return $model::create($data);
                })
                ->createAnother(false)
                ->after(function ($record) {
                    return redirect(
                        static::$resource::getUrl('edit', ['record' => $record]),
                    );
                })
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('recruitments::filament/clusters/applications/resources/applicant/pages/list-applicant.notification.title'))
                        ->body(__('recruitments::filament/clusters/applications/resources/applicant/pages/list-applicant.notification.body')),
                ),
        ];
    }
}
