<?php

namespace Webkul\TimeOff\Filament\Clusters\Management\Resources\TimeOffResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Webkul\TableViews\Filament\Components\PresetView;
use Webkul\TableViews\Filament\Concerns\HasTableViews;
use Webkul\TimeOff\Enums\State;
use Webkul\TimeOff\Filament\Clusters\Management\Resources\TimeOffResource;

class ListTimeOffs extends ListRecords
{
    use HasTableViews;

    protected static string $resource = TimeOffResource::class;

    public function getPresetTableViews(): array
    {
        return [
            'waiting_for_me' => PresetView::make(__('Waiting For Me'))
                ->icon('heroicon-o-user-circle')
                ->favorite()
                ->default()
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('state', [
                    State::CONFIRM->value,
                    State::VALIDATE_ONE->value,
                ])),
            'second_approval' => PresetView::make(__('Second Approval'))
                ->icon('heroicon-o-shield-check')
                ->favorite()
                ->default()
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('state', [
                    State::CONFIRM->value,
                    State::VALIDATE_TWO->value,
                ])),
            'approved' => PresetView::make(__('Approved'))
                ->icon('heroicon-o-check-badge')
                ->favorite()
                ->default()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('state', State::VALIDATE_TWO->value)),
            'valid' => PresetView::make(__('Currently Valid'))
                ->icon('heroicon-o-check')
                ->default()
                ->modifyQueryUsing(function (Builder $query) {
                    $today = now()->format('Y-m-d');

                    return $query
                        ->where(function ($query) use ($today) {
                            $query
                                ->whereDate('date_from', '<=', $today)
                                ->whereDate('date_to', '>=', $today);
                        });
                }),
            'my_team' => PresetView::make(__('My Team'))
                ->icon('heroicon-o-users')
                ->default()
                ->modifyQueryUsing(function (Builder $query) {
                    $currentUserId = Auth::user()->id;

                    return $query->whereHas('employee', function ($query) use ($currentUserId) {
                        $query->where('leave_manager_id', '=', $currentUserId)
                            ->orWhere('user_id', '=', $currentUserId);
                    });
                }),
            'my_department' => PresetView::make(__('My Team'))
                ->icon('heroicon-o-building-office')
                ->default()
                ->modifyQueryUsing(function (Builder $query) {
                    $currentUserId = Auth::user()->id;

                    return $query->whereHas('employee', function ($query) use ($currentUserId) {
                        $query->whereHas('parent', function ($query) use ($currentUserId) {
                            $query->where('user_id', '=', $currentUserId);
                        });
                    });
                }),
            'refused' => PresetView::make(__('Refused'))
                ->icon('heroicon-o-x-circle')
                ->default()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('state', State::REFUSE->value)),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
