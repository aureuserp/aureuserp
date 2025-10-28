<?php

namespace Webkul\Chatter\Livewire;

use Carbon\Carbon;
use Closure;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use Livewire\Component;
use Livewire\WithFileUploads;
use Throwable;
use Webkul\Chatter\Filament\Actions\Chatter\FileAction;
use Webkul\Chatter\Filament\Actions\Chatter\FiltersAction;
use Webkul\Chatter\Filament\Actions\Chatter\FollowerAction;
use Webkul\Chatter\Filament\Infolists\Components\Activities\ActivitiesRepeatableEntry;
use Webkul\Chatter\Filament\Infolists\Components\Activities\ContentTextEntry as ActivityContentTextEntry;
use Webkul\Chatter\Filament\Infolists\Components\Activities\TitleTextEntry as ActivityTitleTextEntry;
use Webkul\Chatter\Filament\Infolists\Components\Messages\ContentTextEntry as MessageContentTextEntry;
use Webkul\Chatter\Filament\Infolists\Components\Messages\MessageRepeatableEntry;
use Webkul\Chatter\Filament\Infolists\Components\Messages\TitleTextEntry as MessageTitleTextEntry;
use Webkul\Chatter\Models\Message;
use Webkul\Partner\Models\Partner;
use Webkul\Security\Models\User;
use Webkul\Support\Models\ActivityPlan;
use Webkul\Support\Models\ActivityType;

class ChatterPanel extends Component implements HasActions, HasForms, HasInfolists
{
    use InteractsWithActions, InteractsWithForms, InteractsWithInfolists, WithFileUploads;

    public Model $record;

    public string $resourceClass = '';

    public mixed $followerViewMailPath = null;

    public bool $isFollowerActionVisible;

    public bool $isFileActionVisible;

    public array $filters;

    public string $search = '';

    public string $filterType = 'all';

    public ?string $dateRange = null;

    public bool $pinnedOnly = false;

    public string $viewMode = 'detailed';

    public string $sortBy = 'created_at_desc';

    public string $tab = 'messages';

    public int $refreshTick = 0;

    protected $listeners = [
        'chatter.refresh' => 'refreshMessages',
    ];

    public function mount(
        Model $record,
        string $resourceClass = '',
        string|Closure|null $followerViewMailPath = null,
        bool $isFollowerActionVisible = true,
        bool $isFileActionVisible = true,
        array $filters = [],
    ): void {
        $this->record = $record;

        $this->followerViewMailPath = $followerViewMailPath;

        $this->resourceClass = $resourceClass;

        $this->isFollowerActionVisible = $isFollowerActionVisible;

        $this->isFileActionVisible = $isFileActionVisible;

        $this->filters = $filters;
    }

    public function getTotalMessages(): int
    {
        return $this->getBaseQuery()->count();
    }

    public function getUnreadCount(): int
    {
        return $this->getBaseQuery()->whereNull('read_at')->count();
    }

    public function hasActiveFilters(): bool
    {
        return filled($this->search)
            || $this->filterType !== 'all'
            || filled($this->dateRange)
            || $this->pinnedOnly;
    }

    public function getActiveFilters(): array
    {
        $filters = [];

        if (filled($this->search)) {
            $filters[] = [
                'key'   => 'search',
                'label' => "Search: \"{$this->search}\"",
            ];
        }

        if ($this->filterType !== 'all') {
            $filters[] = [
                'key'   => 'filterType',
                'label' => 'Type: '.ucfirst($this->filterType),
            ];
        }

        if (filled($this->dateRange)) {
            $filters[] = [
                'key'   => 'dateRange',
                'label' => 'Date: '.$this->getDateRangeLabel(),
            ];
        }

        if ($this->pinnedOnly) {
            $filters[] = [
                'key'   => 'pinnedOnly',
                'label' => 'Pinned only',
            ];
        }

        return $filters;
    }

    public function removeFilter(string $key): void
    {
        match ($key) {
            'search'     => $this->search = '',
            'filterType' => $this->filterType = 'all',
            'dateRange'  => $this->dateRange = null,
            'pinnedOnly' => $this->pinnedOnly = false,
        };
    }

    public function clearAllFilters(): void
    {
        $this->reset(['search', 'filterType', 'dateRange', 'pinnedOnly']);
    }

    public function setViewMode(string $mode): void
    {
        $this->viewMode = $mode;
    }

    public function setTab(string $tab): void
    {
        $this->tab = in_array($tab, ['messages', 'activities'], true) ? $tab : 'messages';
    }

    public function isEmpty(): bool
    {
        return $this->getFilteredQuery()->isEmpty();
    }

    public function hasFilters(): bool
    {
        return $this->hasActiveFilters();
    }

    public function getFilteredCount(): int
    {
        return $this->getFilteredQuery()->count();
    }

    public function getTotalCount(): int
    {
        return $this->getBaseQuery()->count();
    }

    public function canViewAllActivities(): bool
    {
        return true;
    }

    public function filtersAction(): FiltersAction
    {
        return FiltersAction::make('filters')
            ->visible(fn () => $this->tab === 'messages');
    }

    private function getDateRangeLabel(): string
    {
        return match ($this->dateRange) {
            'today'     => 'Today',
            'yesterday' => 'Yesterday',
            'week'      => 'Last 7 days',
            'month'     => 'Last 30 days',
            'quarter'   => 'Last 3 months',
            'year'      => 'Last year',
            default     => 'Unknown range',
        };
    }

    private function getBaseQuery()
    {
        try {
            $this->record->unsetRelation('messages');

            $this->record->unsetRelation('activities');
        } catch (Throwable $e) {
        }

        return $this->record->withFilters($this->filters);
    }

    private function getFilteredQuery()
    {
        $state = $this->getBaseQuery();

        if ($state instanceof Collection) {
            $query = trim(strtolower($this->search));

            if ($query !== '') {
                $state = $state->filter(function ($m) use ($query) {
                    $subject = strtolower((string) data_get($m, 'subject', ''));
                    $body = strtolower(strip_tags((string) data_get($m, 'body', '')));

                    return str_contains($subject, $query) || str_contains($body, $query);
                });
            }

            if ($this->filterType !== 'all') {
                $state = $state->where('type', $this->filterType);
            }

            if (filled($this->dateRange)) {
                $state = $this->applyDateRangeFilter($state);
            }

            if ($this->pinnedOnly) {
                $state = $state->filter(fn ($m) => ! empty($m->pinned_at));
            }

            $state = $this->applySorting($state);
        }

        return $state;
    }

    private function applyDateRangeFilter($state)
    {
        $now = now();

        return $state->filter(function ($message) use ($now) {
            $createdAt = Carbon::parse($message->created_at);

            return match ($this->dateRange) {
                'today'     => $createdAt->isToday(),
                'yesterday' => $createdAt->isYesterday(),
                'week'      => $createdAt->gte($now->copy()->subWeek()),
                'month'     => $createdAt->gte($now->copy()->subMonth()),
                'quarter'   => $createdAt->gte($now->copy()->subMonths(3)),
                'year'      => $createdAt->gte($now->copy()->subYear()),
                default     => true,
            };
        });
    }

    private function applySorting($state)
    {
        return $state->sort(function ($a, $b) {
            $pa = ! empty($a->pinned_at);
            $pb = ! empty($b->pinned_at);

            if ($pa !== $pb) {
                return $pa ? -1 : 1;
            }

            return match ($this->sortBy) {
                'created_at_asc'  => $a->created_at <=> $b->created_at,
                'updated_at_desc' => $b->updated_at <=> $a->updated_at,
                'priority'        => $this->comparePriority($a, $b),
                default           => $b->created_at <=> $a->created_at,
            };
        })->values();
    }

    private function comparePriority($a, $b): int
    {
        $priorityOrder = ['notification' => 4, 'activity' => 3, 'note' => 2, 'comment' => 1];

        $priorityA = $priorityOrder[$a->type] ?? 0;

        $priorityB = $priorityOrder[$b->type] ?? 0;

        return $priorityB <=> $priorityA;
    }

    public function fileAction(): FileAction
    {
        return FileAction::make('file')
            ->visible($this->isFileActionVisible)
            ->hiddenLabel()
            ->record($this->record);
    }

    public function followerAction(): FollowerAction
    {
        return FollowerAction::make('follower')
            ->visible($this->isFollowerActionVisible)
            ->setFollowerMailView($this->followerViewMailPath)
            ->setResource($this->resourceClass)
            ->record($this->record);
    }

    public function removeFollower($partnerId)
    {
        $partner = Partner::findOrFail($partnerId);

        $this->record->removeFollower($partner);

        try {
            $this->record->unsetRelation('followers');
        } catch (Throwable $e) {
        }

        $this->dispatch('chatter.refresh');
    }

    public function refreshMessages(): void
    {
        $this->record->refresh();

        try {
            $this->record->unsetRelation('messages');
            $this->record->unsetRelation('activities');
        } catch (Throwable $e) {
        }

        $this->refreshTick++;

        $this->dispatch('$refresh');
    }

    public function markAsDoneAction(): Action
    {
        return Action::make('markAsDone')
            ->icon('heroicon-o-check-circle')
            ->color('success')
            ->modalIcon('heroicon-o-check-circle')
            ->label(__('chatter::livewire/chatter-panel.mark-as-done.title'))
            ->schema(fn (Schema $schema) => $schema->components([
                TextInput::make('feedback')
                    ->label(__('chatter::livewire/chatter-panel.mark-as-done.form.fields.feedback')),

                Hidden::make('type'),
            ]))
            ->modalFooterActions(fn ($livewire, $arguments): array => [
                Action::make('doneAndScheduleNext')
                    ->icon('heroicon-o-arrow-uturn-right')
                    ->label(__('chatter::livewire/chatter-panel.mark-as-done.footer-actions.label'))
                    ->modalIcon('heroicon-o-arrow-uturn-right')
                    ->action(function (array $data) use ($arguments) {
                        $this->processMessage($arguments['id'], $data['feedback'] ?? null);

                        $this->replaceMountedAction('activity');

                        Notification::make()
                            ->success()
                            ->title(__('chatter::livewire/chatter-panel.mark-as-done.footer-actions.actions.notification.mark-as-done.title'))
                            ->body(__('chatter::livewire/chatter-panel.mark-as-done.footer-actions.actions.notification.mark-as-done.body'))
                            ->send();
                    })
                    ->cancelParentActions(),
                Action::make('done')
                    ->icon('heroicon-o-check-circle')
                    ->label('Done')
                    ->modalIcon('heroicon-o-check-circle')
                    ->action(function (array $data) use ($arguments) {
                        $this->processMessage($arguments['id'], $data['feedback'] ?? null);

                        Notification::make()
                            ->success()
                            ->title(__('chatter::livewire/chatter-panel.mark-as-done.footer-actions.actions.notification.mark-as-done.title'))
                            ->body(__('chatter::livewire/chatter-panel.mark-as-done.footer-actions.actions.notification.mark-as-done.body'))
                            ->send();
                    })
                    ->cancelParentActions(),
            ]);
    }

    protected function processMessage(int $messageId, ?string $feedback): void
    {
        $message = Message::find($messageId);

        if (! $message) {
            return;
        }

        $this->record->addMessage([
            'type' => 'comment',
            'body' => collect([
                $message->activityType?->name ? $message->activityType?->name.' done' : null,
                $message->summary ? $message->summary : null,
                $message->body ? __('chatter::livewire/chatter-panel.process-message.original-note', ['body' => $message->body]) : null,
                $feedback ? __('chatter::livewire/chatter-panel.process-message.feedback', ['feedback' => $feedback]) : null,
            ])->filter()->implode(''),
        ]);

        $message->delete();
    }

    public function editActivityAction(): Action
    {
        return Action::make('editActivity')
            ->icon('heroicon-o-pencil-square')
            ->modalIcon('heroicon-o-pencil-square')
            ->color('primary')
            ->label(__('chatter::livewire/chatter-panel.edit-activity.title'))
            ->mountUsing(function (Schema $schema, $livewire) {
                $activityId = $livewire->mountedActionsArguments[0]['id'];
                $record = Message::find($activityId);

                $schema->fill([
                    'activity_plan_id' => $record->activity_plan_id,
                    'activity_type_id' => $record->activity_type_id,
                    'date_deadline'    => $record->date_deadline,
                    'summary'          => $record->summary,
                    'assigned_to'      => $record->assigned_to,
                    'body'             => $record->body,
                    'type'             => $record->type ?? 'activity',
                ]);
            })
            ->schema(function (Schema $schema) {
                return $schema->components([
                    Group::make()
                        ->schema([
                            Group::make()
                                ->schema([
                                    Select::make('activity_plan_id')
                                        ->label(__('chatter::livewire/chatter-panel.edit-activity.form.fields.activity-plan'))
                                        ->options($this->activityPlans)
                                        ->searchable()
                                        ->preload()
                                        ->live(),
                                    DatePicker::make('date_deadline')
                                        ->label(__('chatter::livewire/chatter-panel.edit-activity.form.fields.plan-date'))
                                        ->hidden(fn (Get $get) => ! $get('activity_plan_id'))
                                        ->live()
                                        ->native(false),
                                ])
                                ->columns(2),

                            Group::make()
                                ->schema([
                                    Placeholder::make('plan_summary')
                                        ->label(__('chatter::livewire/chatter-panel.edit-activity.form.fields.plan-summary'))
                                        ->content(function (Get $get) {
                                            if (! $get('activity_plan_id')) {
                                                return null;
                                            }

                                            $activityPlanTemplates = ActivityPlan::find($get('activity_plan_id'))
                                                ->activityPlanTemplates;

                                            $html = '<div class="space-y-2">';
                                            foreach ($activityPlanTemplates as $activityPlanTemplate) {
                                                $planDate = $get('date_deadline') ? Carbon::parse($get('date_deadline'))->format('m/d/Y') : '';
                                                $html .= '<div class="flex items-center space-x-2" style="margin-left: 20px;">
                                                            <span>•</span>
                                                            <span style="margin-left:2px;">'.$activityPlanTemplate->summary.($planDate ? ' ('.$planDate.')' : '').'</span>
                                                          </div>';
                                            }
                                            $html .= '</div>';

                                            return new HtmlString($html);
                                        })->hidden(fn (Get $get) => ! $get('activity_plan_id')),
                                    Select::make('activity_type_id')
                                        ->label(__('chatter::livewire/chatter-panel.edit-activity.form.fields.activity-type'))
                                        ->options(ActivityType::pluck('name', 'id'))
                                        ->searchable()
                                        ->preload()
                                        ->live()
                                        ->required()
                                        ->visible(fn (Get $get) => ! $get('activity_plan_id')),
                                    DatePicker::make('date_deadline')
                                        ->label(__('chatter::livewire/chatter-panel.edit-activity.form.fields.due-date'))
                                        ->native(false)
                                        ->hidden(fn (Get $get) => $get('activity_type_id') ? ActivityType::find($get('activity_type_id'))?->category == 'meeting' : false)
                                        ->visible(fn (Get $get) => ! $get('activity_plan_id')),
                                    TextInput::make('summary')
                                        ->label(__('chatter::livewire/chatter-panel.edit-activity.form.fields.summary'))
                                        ->visible(fn (Get $get) => ! $get('activity_plan_id')),
                                    Select::make('assigned_to')
                                        ->label(__('chatter::livewire/chatter-panel.edit-activity.form.fields.assigned-to'))
                                        ->searchable()
                                        ->hidden(fn (Get $get) => $get('activity_type_id') ? ActivityType::find($get('activity_type_id'))?->category == 'meeting' : false)
                                        ->live()
                                        ->visible(fn (Get $get) => ! $get('activity_plan_id'))
                                        ->options(User::all()->pluck('name', 'id')->toArray())
                                        ->required(),
                                ])->columns(2),
                            RichEditor::make('body')
                                ->hiddenLabel()
                                ->hidden(fn (Get $get) => $get('activity_type_id') ? ActivityType::find($get('activity_type_id'))?->category == 'meeting' : false)
                                ->visible(fn (Get $get) => ! $get('activity_plan_id'))
                                ->label(__('chatter::app.filament.actions.chatter.activity.form.type-your-message-here'))
                                ->visible(fn (Get $get) => ! $get('activity_plan_id')),
                            Hidden::make('type')
                                ->default('activity'),
                        ]),
                ]);
            })
            ->action(function (array $data, $livewire) {
                $activityId = $livewire->mountedActionsArguments[0]['id'];

                $record = Message::find($activityId);

                $record->update($data);

                Notification::make()
                    ->success()
                    ->title(__('chatter::livewire/chatter-panel.edit-activity.action.notification.success.title'))
                    ->body(__('chatter::livewire/chatter-panel.edit-activity.action.notification.success.body'))
                    ->send();
            });
    }

    public function deleteMessageAction(): Action
    {
        return Action::make('deleteMessage')
            ->requiresConfirmation()
            ->action(function (array $arguments) {
                $this->record->removeMessage($arguments['id']);
                $this->refreshMessages();
            });
    }

    public function cancelActivityAction(): Action
    {
        return Action::make('cancelActivity')
            ->icon('heroicon-o-trash')
            ->label(__('chatter::livewire/chatter-panel.cancel-activity-plan-action.title'))
            ->color('danger')
            ->requiresConfirmation()
            ->action(function (array $arguments) {
                $this->record->removeMessage($arguments['id'], 'activities');
                $this->refreshMessages();
            });
    }

    public function chatInfolist(Schema $schema): Schema
    {
        $state = $this->getFilteredQuery();

        return $schema
            ->record($this->record)
            ->components([
                MessageRepeatableEntry::make('messages')
                    ->state($state)
                    ->hiddenLabel()
                    ->gap(false)
                    ->schema([
                        MessageTitleTextEntry::make('user')
                            ->hiddenLabel(),
                        MessageContentTextEntry::make('content')
                            ->hiddenLabel(),
                    ])
                    ->placeholder(__('chatter::livewire/chatter-panel.placeholders.no-record-found')),
            ]);
    }

    public function pinMessage(int $id): void
    {
        $message = Message::find($id);

        if (! $message) {
            return;
        }

        $message->update([
            'pinned_at' => $message->pinned_at ? null : now(),
        ]);

        $this->refreshMessages();
    }

    public function activityInfolist(Schema $schema): Schema
    {
        return $schema
            ->record($this->record)
            ->components(function () {
                return $this->record->activities->isEmpty() ? [] : [
                    Section::make(__('chatter::livewire/chatter-panel.activity-infolist.title'))
                        ->collapsible()
                        ->extraAttributes(['class' => 'm-1'])
                        ->compact()
                        ->schema([
                            ActivitiesRepeatableEntry::make('activities')
                                ->hiddenLabel()
                                ->schema([
                                    ActivityTitleTextEntry::make('user')
                                        ->hiddenLabel(),
                                    ActivityContentTextEntry::make('content')
                                        ->hiddenLabel(),
                                ])
                                ->placeholder(__('chatter::livewire/chatter-panel.placeholders.no-record-found')),
                        ]),
                ];
            });
    }

    public function placeholder()
    {
        return <<<'HTML'
            <div class="flex items-center justify-center w-full">
                <div class="flex flex-col items-center space-y-4">
                    <x-filament::loading-indicator class="w-10 h-10 text-primary-500 animate-spin" />
                    <p class="text-sm font-medium tracking-wide text-gray-600 dark:text-gray-300">
                        {{ __('chatter::livewire/chatter-panel.placeholders.loading') }}
                    </p>
                </div>
            </div>
        HTML;
    }

    public function render(): View
    {
        return view('chatter::livewire.chatter-panel');
    }
}
