<?php

namespace Webkul\Maintenance\Filament\Widgets;

use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Webkul\FullCalendar\Filament\Actions\CreateAction;
use Webkul\FullCalendar\Filament\Actions\ViewAction;
use Webkul\FullCalendar\Filament\Widgets\FullCalendarWidget;
use Webkul\Maintenance\Enums\MaintenanceRequestType;
use Webkul\Maintenance\Filament\Clusters\Maintenance\Resources\MaintenanceRequestResource;
use Webkul\Maintenance\Models\MaintenanceRequest;
use Webkul\Maintenance\Models\Stage;
use Webkul\Maintenance\Models\Team;

class MaintenanceCalendarWidget extends FullCalendarWidget
{
    public Model|string|null $model = MaintenanceRequest::class;

    public ?string $slotScheduledAt = null;

    public function getHeading(): string|Htmlable|null
    {
        return __('maintenance::filament/widgets/maintenance-calendar-widget.heading.title');
    }

    public function config(): array
    {
        return [
            'initialView'      => 'dayGridMonth',
            'headerToolbar'    => [
                'left'   => 'prev,next today',
                'center' => 'title',
                'right'  => 'multiMonthYear,dayGridMonth,timeGridWeek,listWeek',
            ],
            'buttonText'       => [
                'today'        => __('maintenance::filament/widgets/maintenance-calendar-widget.config.button-text.today'),
                'dayGridMonth' => __('maintenance::filament/widgets/maintenance-calendar-widget.config.button-text.month'),
                'timeGridWeek' => __('maintenance::filament/widgets/maintenance-calendar-widget.config.button-text.week'),
                'listWeek'     => __('maintenance::filament/widgets/maintenance-calendar-widget.config.button-text.list'),
            ],
            'views'            => [
                'multiMonthYear' => [
                    'buttonText' => __('maintenance::filament/widgets/maintenance-calendar-widget.config.button-text.year'),
                ],
            ],
            'height'           => 'auto',
            'aspectRatio'      => 1.8,
            'firstDay'         => 1,
            'moreLinkClick'    => 'popover',
            'eventDisplay'     => 'block',
            'displayEventTime' => false,
            'selectable'       => true,
            'selectMirror'     => true,
            'unselectAuto'     => false,
        ];
    }

    protected function headerActions(): array
    {
        return [
            CreateAction::make('create')
                ->icon('heroicon-o-plus-circle')
                ->modalIcon('heroicon-o-calendar-days')
                ->label(__('maintenance::filament/widgets/maintenance-calendar-widget.header-actions.create.label'))
                ->modalHeading(__('maintenance::filament/widgets/maintenance-calendar-widget.header-actions.create.modal-heading'))
                ->color('success')
                ->action(fn (array $data) => $this->createMaintenanceRequest($data))
                ->mountUsing(function (Schema $schema, array $arguments): void {
                    $scheduledAt = $arguments['scheduled_at'] ?? $arguments['start'] ?? null;

                    $this->slotScheduledAt = $scheduledAt
                        ? Carbon::parse($scheduledAt)->toDateTimeString()
                        : null;

                    $schema->fill([
                        'scheduled_at' => $this->slotScheduledAt ?? now(),
                    ]);
                }),
        ];
    }

    public function modalActions(): array
    {
        return [
            Action::make('edit')
                ->label(__('maintenance::filament/widgets/maintenance-calendar-widget.modal-actions.edit.label'))
                ->color('primary')
                ->url(fn (): ?string => $this->record ? MaintenanceRequestResource::getUrl('edit', ['record' => $this->record]) : null),
        ];
    }

    protected function viewAction(): Action
    {
        return ViewAction::make()
            ->modalIcon('heroicon-o-wrench-screwdriver')
            ->icon('heroicon-o-eye')
            ->label(__('maintenance::filament/widgets/maintenance-calendar-widget.view-action.label'))
            ->modalHeading(fn (): ?string => $this->record?->name)
            ->schema($this->infolist());
    }

    public function getFormSchema(): array
    {
        return [
            TextInput::make('name')
                ->label(__('maintenance::filament/widgets/maintenance-calendar-widget.form.fields.subject'))
                ->required()
                ->maxLength(255)
                ->autofocus()
                ->columnSpanFull(),

            $this->slotScheduledAt
                ? Hidden::make('scheduled_at')
                : DateTimePicker::make('scheduled_at')
                    ->label(__('maintenance::filament/widgets/maintenance-calendar-widget.form.fields.scheduled-at'))
                    ->required()
                    ->seconds(false)
                    ->native(false)
                    ->default(now()),
        ];
    }

    public function infolist(): array
    {
        return [
            Section::make()
                ->schema([
                    Grid::make(1)
                        ->schema([
                            TextEntry::make('scheduled_date')
                                ->label(__('maintenance::filament/widgets/maintenance-calendar-widget.infolist.entries.date'))
                                ->state(fn (MaintenanceRequest $record): ?string => $record->scheduled_at?->format('M d, Y'))
                                ->icon('heroicon-o-calendar-days')
                                ->placeholder('—'),

                            TextEntry::make('scheduled_time')
                                ->label(__('maintenance::filament/widgets/maintenance-calendar-widget.infolist.entries.time'))
                                ->state(function (MaintenanceRequest $record): ?string {
                                    if (! $record->scheduled_at) {
                                        return null;
                                    }

                                    $startsAt = $record->scheduled_at;
                                    $endsAt = $startsAt->copy()->addHours((float) ($record->duration ?: 0));
                                    $duration = format_float_time((float) ($record->duration ?: 0), 'hours');

                                    if (! $record->duration) {
                                        return $startsAt->format('H:i');
                                    }

                                    return $startsAt->format('H:i').' - '.$endsAt->format('H:i').' ('.$duration.')';
                                })
                                ->icon('heroicon-o-clock')
                                ->placeholder('—'),
                        ]),

                    Grid::make(2)
                        ->schema([
                            TextEntry::make('user.name')
                                ->label(__('maintenance::filament/widgets/maintenance-calendar-widget.infolist.entries.technician'))
                                ->weight(FontWeight::Medium)
                                ->placeholder('—'),

                            TextEntry::make('priority')
                                ->label(__('maintenance::filament/widgets/maintenance-calendar-widget.infolist.entries.priority'))
                                ->weight(FontWeight::Medium)
                                ->placeholder('—'),

                            TextEntry::make('maintenance_type')
                                ->label(__('maintenance::filament/widgets/maintenance-calendar-widget.infolist.entries.maintenance-type'))
                                ->weight(FontWeight::Medium)
                                ->formatStateUsing(fn (?MaintenanceRequestType $state): string => $state?->getLabel() ?? '—')
                                ->placeholder('—'),

                            TextEntry::make('stage.name')
                                ->label(__('maintenance::filament/widgets/maintenance-calendar-widget.infolist.entries.stage'))
                                ->weight(FontWeight::Medium)
                                ->placeholder('—'),
                        ]),
                ]),
        ];
    }

    public function fetchEvents(array $fetchInfo): array
    {
        return MaintenanceRequest::query()
            ->whereNotNull('scheduled_at')
            ->whereBetween('scheduled_at', [$fetchInfo['start'], $fetchInfo['end']])
            ->with(['stage', 'user'])
            ->get()
            ->map(fn (MaintenanceRequest $request): array => [
                'id'              => $request->id,
                'title'           => $request->name,
                'start'           => $request->scheduled_at?->toIso8601String(),
                'allDay'          => false,
                'backgroundColor' => $request->stage?->done ? '#10B981' : '#3B82F6',
                'borderColor'     => $request->stage?->done ? '#059669' : '#2563EB',
                'textColor'       => '#ffffff',
            ])
            ->all();
    }

    public function onDateSelect(string $start, ?string $end, bool $allDay, ?array $view, ?array $resource): void
    {
        $this->slotScheduledAt = Carbon::parse($start)->toDateTimeString();

        $this->mountAction('create', [
            'scheduled_at' => $this->slotScheduledAt,
        ]);
    }

    public function createMaintenanceRequest(array $data): void
    {
        $stageId = Stage::query()->orderBy('sort')->value('id');
        $teamId = Team::query()->value('id');

        if (! $stageId || ! $teamId) {
            Notification::make()
                ->danger()
                ->title(__('maintenance::filament/widgets/maintenance-calendar-widget.header-actions.create.notification.error.title'))
                ->body(__('maintenance::filament/widgets/maintenance-calendar-widget.header-actions.create.notification.error.body'))
                ->send();

            return;
        }

        MaintenanceRequest::query()->create([
            'name'                => $data['name'],
            'requested_at'        => Carbon::parse($data['scheduled_at'] ?? now())->toDateString(),
            'scheduled_at'        => $data['scheduled_at'] ?? now(),
            'maintenance_type'    => MaintenanceRequestType::CORRECTIVE,
            'stage_id'            => $stageId,
            'user_id'             => Auth::id(),
            'maintenance_team_id' => $teamId,
            'company_id'          => Auth::user()?->default_company_id,
        ]);

        Notification::make()
            ->success()
            ->title(__('maintenance::filament/widgets/maintenance-calendar-widget.header-actions.create.notification.success.title'))
            ->body(__('maintenance::filament/widgets/maintenance-calendar-widget.header-actions.create.notification.success.body'))
            ->send();

        $this->slotScheduledAt = null;

        $this->refreshRecords();
    }
}
