<?php

namespace Webkul\TimeOff\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Webkul\FullCalendar\Filament\Actions\CreateAction;
use Webkul\FullCalendar\Filament\Actions\DeleteAction;
use Webkul\FullCalendar\Filament\Actions\EditAction;
use Webkul\FullCalendar\Filament\Actions\ViewAction;
use Webkul\FullCalendar\Filament\Widgets\FullCalendarWidget;
use Webkul\TimeOff\Enums\State;
use Webkul\TimeOff\Models\Leave;
use Webkul\TimeOff\Traits\TimeOffHelper;

class OverviewCalendarWidget extends FullCalendarWidget
{
    use TimeOffHelper;

     use HasWidgetShield;
    public Model|string|null $model = Leave::class;

    public function getHeading(): string|Htmlable|null
    {
        return __('time-off::filament/widgets/overview-calendar-widget.heading.title');
    }

    public function config(): array
    {
        return [
            'initialView' => 'multiMonthYear',
        ];
    }

    public function modalActions(): array
    {
        return [
            EditAction::make()
                ->label(__('time-off::filament/widgets/overview-calendar-widget.modal-actions.edit.title'))
                ->action(function ($data, $record, EditAction $action) {
                    $data = $this->mutateTimeOffData($data, $this->record?->id, $action);

                    $record->update($data);

                    Notification::make()
                        ->success()
                        ->title(__('time-off::filament/widgets/overview-calendar-widget.modal-actions.edit.notification.title'))
                        ->body(__('time-off::filament/widgets/overview-calendar-widget.modal-actions.edit.notification.body'))
                        ->send();
                    $action->cancel();
                })
                ->mountUsing(
                    function (Schema $schema, array $arguments, $livewire) {
                        $leave = $livewire->record;

                        $newData = [
                            ...$leave->toArray(),
                            'request_date_from' => $arguments['event']['start'] ?? $leave->request_date_from,
                            'request_date_to'   => $arguments['event']['end'] ?? $leave->request_date_to,
                        ];

                        $schema->fill($newData);
                    }
                ),
            DeleteAction::make()
                ->label(__('time-off::filament/widgets/overview-calendar-widget.modal-actions.delete.title')),
        ];
    }

    protected function viewAction(): Action
    {
        return ViewAction::make()
            ->modalIcon('heroicon-o-lifebuoy')
            ->label(__('time-off::filament/widgets/overview-calendar-widget.view-action.title'))
            ->modalDescription(__('time-off::filament/widgets/overview-calendar-widget.view-action.description'))
            ->schema($this->infolist());
    }

    protected function headerActions(): array
    {
        return [
            CreateAction::make()
                ->icon('heroicon-o-plus-circle')
                ->modalIcon('heroicon-o-lifebuoy')
                ->label(__('time-off::filament/widgets/overview-calendar-widget.header-actions.create.title'))
                ->modalDescription(__('time-off::filament/widgets/overview-calendar-widget.header-actions.create.description'))
                ->action(function ($data, CreateAction $action) {
                    $data = $this->mutateTimeOffData($data, $this->record?->id, $action);
                    Leave::create($data);

                    Notification::make()
                        ->success()
                        ->title(__('time-off::filament/widgets/overview-calendar-widget.header-actions.create.notification.title'))
                        ->body(__('time-off::filament/widgets/overview-calendar-widget.header-actions.create.notification.body'))
                        ->send();

                    $action->cancel();
                })
                ->mountUsing(
                    function (Schema $schema, array $arguments) {
                        $schema->fill($arguments);
                    }
                ),
        ];
    }

    public function infolist(): array
    {
        return [
            TextEntry::make('holidayStatus.name')
                ->label(__('time-off::filament/widgets/overview-calendar-widget.infolist.entries.time-off-type'))
                ->icon('heroicon-o-clock'),
            TextEntry::make('request_date_from')
                ->label(__('time-off::filament/widgets/overview-calendar-widget.infolist.entries.request-date-from'))
                ->date()
                ->icon('heroicon-o-calendar-days'),
            TextEntry::make('request_date_to')
                ->label(__('time-off::filament/widgets/overview-calendar-widget.infolist.entries.request-date-to'))
                ->date()
                ->icon('heroicon-o-calendar-days'),
            TextEntry::make('number_of_days')
                ->label(__('time-off::filament/widgets/overview-calendar-widget.infolist.entries.duration'))
                ->formatStateUsing(fn ($state) => round($state, 1).' day(s)')
                ->icon('heroicon-o-clock'),
            TextEntry::make('private_name')
                ->label(__('time-off::filament/widgets/overview-calendar-widget.infolist.entries.description'))
                ->icon('heroicon-o-document-text')
                ->placeholder(__('time-off::filament/widgets/overview-calendar-widget.infolist.entries.description-placeholder')),
            TextEntry::make('state')
                ->placeholder(__('time-off::filament/widgets/overview-calendar-widget.infolist.entries.status'))
                ->badge()
                ->formatStateUsing(fn ($state) => State::options()[$state->value])
                ->icon('heroicon-o-check-circle'),
        ];
    }

    public function fetchEvents(array $fetchInfo): array
    {
        $user = Auth::user();

        return Leave::query()
            ->where('request_date_from', '>=', $fetchInfo['start'])
            ->where('request_date_to', '<=', $fetchInfo['end'])
            ->with('holidayStatus')
            ->get()
            ->map(function (Leave $leave) {
                return [
                    'id'              => $leave->id,
                    'title'           => $leave->holidayStatus?->name,
                    'start'           => $leave->request_date_from,
                    'end'             => $leave->request_date_to,
                    'allDay'          => true,
                    'backgroundColor' => $leave->holidayStatus?->color,
                    'borderColor'     => $leave->holidayStatus?->color,
                    'textColor'       => '#ffffff',
                ];
            })
            ->all();
    }

    public function onDateSelect(string $start, ?string $end, bool $allDay, ?array $view, ?array $resource): void
    {
        $startDate = Carbon::parse($start);
        $endDate = $end ? Carbon::parse($end) : $startDate;

        $this->mountAction('create', [
            'request_date_from' => $startDate->toDateString(),
            'request_date_to'   => $endDate->toDateString(),
        ]);
    }
}
