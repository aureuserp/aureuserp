<?php

namespace Webkul\Recruitment\Filament\Clusters\Applications\Resources\CandidateResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Webkul\Recruitment\Enums\RecruitmentState;
use Webkul\Recruitment\Filament\Clusters\Applications\Resources\CandidateResource;
use Webkul\TableViews\Filament\Components\PresetView;
use Webkul\TableViews\Filament\Concerns\HasTableViews;

class ListCandidates extends ListRecords
{
    use HasTableViews;

    protected static string $resource = CandidateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->icon('heroicon-o-plus-circle'),
        ];
    }

    public function getPresetTableViews(): array
    {
        return [
            'my_candidate' => PresetView::make(__('recruitments::filament/clusters/applications/resources/candidate/pages/list-candidate.tabs.my-applicants'))
                ->icon('heroicon-s-user-circle')
                ->favorite()
                ->modifyQueryUsing(function (Builder $query) {
                    return $query
                        ->where('creator_id', Auth::id());
                }),
            'un_assigned' => PresetView::make(__('recruitments::filament/clusters/applications/resources/candidate/pages/list-candidate.tabs.un-assigned'))
                ->icon('heroicon-s-user-minus')
                ->favorite()
                ->modifyQueryUsing(function (Builder $query) {
                    return $query
                        ->whereNull('manager_id');
                }),
            'in_progress' => PresetView::make(__('recruitments::filament/clusters/applications/resources/candidate/pages/list-candidate.tabs.in-progress'))
                ->icon('heroicon-s-arrow-path')
                ->favorite()
                ->modifyQueryUsing(function (Builder $query) {
                    return $query
                        ->join('recruitments_applicants', 'recruitments_applicants.candidate_id', '=', 'recruitments_candidates.id')
                        ->whereNull('recruitments_applicants.deleted_at')
                        ->where('recruitments_applicants.is_active', true)
                        ->whereNull('recruitments_applicants.refuse_reason_id')
                        ->whereNull('recruitments_applicants.date_closed')
                        ->whereNull('recruitments_candidates.deleted_at')
                        ->select('recruitments_candidates.*')
                        ->orderBy('recruitments_applicants.created_at', 'desc')
                        ->orderBy('recruitments_candidates.created_at', 'desc');
                }),
            'hired' => PresetView::make(__('recruitments::filament/clusters/applications/resources/candidate/pages/list-candidate.tabs.hired'))
                ->icon('heroicon-s-check-badge')
                ->modifyQueryUsing(function (Builder $query) {
                    return $query
                        ->join('recruitments_applicants', 'recruitments_applicants.candidate_id', '=', 'recruitments_candidates.id')
                        ->whereNull('recruitments_applicants.deleted_at')
                        ->where('recruitments_applicants.is_active', true)
                        ->whereNotNull('recruitments_applicants.date_closed')
                        ->whereNull('recruitments_candidates.deleted_at')
                        ->select('recruitments_candidates.*')
                        ->orderBy('recruitments_applicants.created_at', 'desc')
                        ->orderBy('recruitments_candidates.created_at', 'desc');
                }),
            'refused' => PresetView::make(__('recruitments::filament/clusters/applications/resources/candidate/pages/list-candidate.tabs.refused'))
                ->icon('heroicon-s-no-symbol')
                ->modifyQueryUsing(function (Builder $query) {
                    return $query
                        ->join('recruitments_applicants', 'recruitments_applicants.candidate_id', '=', 'recruitments_candidates.id')
                        ->whereNull('recruitments_applicants.deleted_at')
                        ->where('recruitments_applicants.is_active', true)
                        ->whereNotNull('recruitments_applicants.refuse_reason_id')
                        ->whereNull('recruitments_candidates.deleted_at')
                        ->select('recruitments_candidates.*')
                        ->orderBy('recruitments_applicants.created_at', 'desc')
                        ->orderBy('recruitments_candidates.created_at', 'desc');
                }),
            'archived' => PresetView::make(__('recruitments::filament/clusters/applications/resources/candidate/pages/list-candidate.tabs.archived'))
                ->icon('heroicon-s-archive-box')
                ->modifyQueryUsing(function (Builder $query) {
                    return $query
                        ->join('recruitments_applicants', 'recruitments_applicants.candidate_id', '=', 'recruitments_candidates.id')
                        ->where(function (Builder $subQuery) {
                            $subQuery
                                ->whereNotNull('recruitments_applicants.deleted_at')
                                ->orWhere('recruitments_applicants.is_active', false);
                        })
                        ->whereNull('recruitments_candidates.deleted_at')
                        ->select('recruitments_candidates.*')
                        ->orderBy('recruitments_applicants.created_at', 'desc')
                        ->orderBy('recruitments_candidates.created_at', 'desc');
                }),
            'blocked' => PresetView::make(__('recruitments::filament/clusters/applications/resources/candidate/pages/list-candidate.tabs.blocked'))
                ->icon('heroicon-s-shield-exclamation')
                ->modifyQueryUsing(function (Builder $query) {
                    return $query
                        ->join('recruitments_applicants', 'recruitments_applicants.candidate_id', '=', 'recruitments_candidates.id')
                        ->where(function (Builder $subQuery) {
                            $subQuery
                                ->where('recruitments_applicants.is_active', false)
                                ->orWhere('recruitments_applicants.state', RecruitmentState::BLOCKED->value);
                        })
                        ->whereNull('recruitments_candidates.deleted_at')
                        ->select('recruitments_candidates.*')
                        ->orderBy('recruitments_applicants.created_at', 'desc')
                        ->orderBy('recruitments_candidates.created_at', 'desc');
                }),
            'directly_available' => PresetView::make(__('recruitments::filament/clusters/applications/resources/candidate/pages/list-candidate.tabs.directly-available'))
                ->icon('heroicon-s-clock')
                ->modifyQueryUsing(function (Builder $query) {
                    return $query
                        ->join('recruitments_applicants', 'recruitments_applicants.candidate_id', '=', 'recruitments_candidates.id')
                        ->where(function (Builder $subQuery) {
                            $subQuery
                                ->where('recruitments_candidates.availability_date', '<=', now()->format('Y-m-d'))
                                ->orWhere('recruitments_candidates.availability_date', false);
                        })
                        ->whereNull('recruitments_candidates.deleted_at')
                        ->whereNull('recruitments_applicants.deleted_at')
                        ->select('recruitments_candidates.*')
                        ->orderBy('recruitments_applicants.created_at', 'desc')
                        ->orderBy('recruitments_candidates.created_at', 'desc');
                }),
            'created_recently' => PresetView::make(__('recruitments::filament/clusters/applications/resources/candidate/pages/list-candidate.tabs.created-recently'))
                ->icon('heroicon-s-sparkles')
                ->modifyQueryUsing(function (Builder $query) {
                    return $query
                        ->join('recruitments_applicants', 'recruitments_applicants.candidate_id', '=', 'recruitments_candidates.id')
                        ->where('recruitments_applicants.create_date', '>=', now()->subDays(30)->toDateString())
                        ->whereNull('recruitments_candidates.deleted_at')
                        ->whereNull('recruitments_applicants.deleted_at')
                        ->select('recruitments_candidates.*')
                        ->orderBy('recruitments_applicants.created_at', 'desc')
                        ->orderBy('recruitments_candidates.created_at', 'desc');
                }),
            'stage_updated_recently' => PresetView::make(__('recruitments::filament/clusters/applications/resources/candidate/pages/list-candidate.tabs.stage-updated-recently'))
                ->icon('heroicon-s-arrows-pointing-out')
                ->modifyQueryUsing(function (Builder $query) {
                    return $query
                        ->join('recruitments_applicants', 'recruitments_applicants.candidate_id', '=', 'recruitments_candidates.id')
                        ->where('recruitments_applicants.date_last_stage_updated', '>=', now()->subDays(30))
                        ->whereNull('recruitments_candidates.deleted_at')
                        ->whereNull('recruitments_applicants.deleted_at')
                        ->select('recruitments_candidates.*')
                        ->orderBy('recruitments_applicants.created_at', 'desc')
                        ->orderBy('recruitments_candidates.created_at', 'desc');
                }),
        ];
    }
}
