<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource\Actions;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource;
use Webkul\Crm\Filament\Support\LeadResourceResolver;
use Webkul\Crm\Models\Lead;
use Webkul\Security\Models\User;
use Webkul\Support\Models\ActivityType;

/**
 * Schedules an activity on any opportunity from the My Activities views,
 * without opening the opportunity's chatter first.
 */
class ScheduleActivityAction extends Action
{
    protected ?Lead $lockedLead = null;

    /**
     * Schedules on this lead/opportunity only (used on its own page); the record picker is hidden.
     */
    public function forLead(?Lead $lead): static
    {
        $this->lockedLead = $lead;

        return $this;
    }

    public static function getDefaultName(): ?string
    {
        return 'scheduleActivity';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('crm::filament/clusters/sales/resources/activity.actions.schedule.label'))
            ->icon('heroicon-o-plus-circle')
            ->modalHeading(__('crm::filament/clusters/sales/resources/activity.actions.schedule.modal.heading'))
            ->modalSubmitActionLabel(__('crm::filament/clusters/sales/resources/activity.actions.schedule.modal.submit'))
            ->modalWidth('2xl')
            ->visible(fn (): bool => $this->lockedLead
                ? ! $this->lockedLead->trashed() && LeadResourceResolver::resourceFor($this->lockedLead)::canEdit($this->lockedLead)
                : ActivityResource::canViewAny() && OpportunityResource::canViewAny())
            ->fillForm(fn (array $arguments): array => static::defaultsForType($arguments['column'] ?? null))
            ->schema([
                Select::make('lead_id')
                    ->hidden(fn (): bool => (bool) $this->lockedLead)
                    ->label(__('crm::filament/clusters/sales/resources/activity.actions.schedule.form.opportunity'))
                    ->searchable()
                    ->preload()
                    ->options(fn (): array => static::selectableOpportunities()->latest()->limit(50)->pluck('name', 'id')->all())
                    ->getSearchResultsUsing(fn (string $search): array => static::selectableOpportunities()
                        ->where('name', 'like', "%{$search}%")
                        ->limit(50)
                        ->pluck('name', 'id')
                        ->all())
                    ->getOptionLabelUsing(fn ($value): ?string => static::selectableOpportunities()->find($value)?->name)
                    ->required(fn (): bool => ! $this->lockedLead),
                Grid::make(2)
                    ->schema([
                        Select::make('activity_type_id')
                            ->label(__('crm::filament/clusters/sales/resources/activity.actions.schedule.form.activity-type'))
                            ->options(fn (): array => ActivityType::query()->where('plugin', 'crm')->orderBy('sort')->pluck('name', 'id')->all())
                            ->searchable()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Set $set, mixed $state): void {
                                foreach (static::defaultsForType($state) as $field => $value) {
                                    $set($field, $value);
                                }
                            }),
                        DatePicker::make('date_deadline')
                            ->label(__('crm::filament/clusters/sales/resources/activity.actions.schedule.form.due-date'))
                            ->native(false)
                            ->required(),
                        TextInput::make('summary')
                            ->label(__('crm::filament/clusters/sales/resources/activity.actions.schedule.form.summary'))
                            ->maxLength(255),
                        Select::make('assigned_to')
                            ->label(__('crm::filament/clusters/sales/resources/activity.actions.schedule.form.assigned-to'))
                            ->options(fn (): array => User::query()->orderBy('name')->pluck('name', 'id')->all())
                            ->searchable()
                            ->required(),
                    ]),
                RichEditor::make('body')
                    ->label(__('crm::filament/clusters/sales/resources/activity.actions.schedule.form.note')),
            ])
            ->action(function (array $data): void {
                $lead = $this->lockedLead ?? static::selectableOpportunities()->find($data['lead_id'] ?? null);

                if (! $lead || ! LeadResourceResolver::resourceFor($lead)::canEdit($lead)) {
                    Notification::make()
                        ->danger()
                        ->title(__('crm::filament/clusters/sales/resources/activity.actions.schedule.notification.forbidden.title'))
                        ->body(__('crm::filament/clusters/sales/resources/activity.actions.schedule.notification.forbidden.body'))
                        ->send();

                    return;
                }

                $user = Auth::user();

                $lead->addMessage([
                    'type'             => 'activity',
                    'activity_type_id' => $data['activity_type_id'],
                    'date_deadline'    => $data['date_deadline'],
                    'summary'          => $data['summary'] ?? null,
                    'assigned_to'      => $data['assigned_to'],
                    'body'             => filled(trim(strip_tags((string) ($data['body'] ?? '')))) ? $data['body'] : null,
                    'causer_type'      => $user?->getMorphClass(),
                    'causer_id'        => $user?->getKey(),
                ], $user?->getKey());

                Notification::make()
                    ->success()
                    ->title(__('crm::filament/clusters/sales/resources/activity.actions.schedule.notification.success.title'))
                    ->body(__('crm::filament/clusters/sales/resources/activity.actions.schedule.notification.success.body', ['opportunity' => $lead->name]))
                    ->send();
            });
    }

    /**
     * Open opportunities reachable through the Opportunity resource (company-scoped, lost ones excluded).
     * Edit permission on the chosen opportunity is still checked when saving.
     */
    protected static function selectableOpportunities(): Builder
    {
        return OpportunityResource::getEloquentQuery()->withoutTrashed();
    }

    /**
     * Form defaults derived from an activity type: its due-date delay, summary and default user.
     *
     * @return array{activity_type_id: mixed, date_deadline: string, summary: ?string, assigned_to: ?int}
     */
    public static function defaultsForType(mixed $activityTypeId): array
    {
        $type = filled($activityTypeId) ? ActivityType::query()->find($activityTypeId) : null;

        $unit = $type?->delay_unit instanceof BackedEnum ? $type->delay_unit->value : ($type?->delay_unit ?: 'days');

        return [
            'activity_type_id' => $type?->getKey(),
            'date_deadline'    => now()->add($unit, (int) ($type?->delay_count ?? 0))->toDateString(),
            'summary'          => $type?->summary,
            'assigned_to'      => $type?->default_user_id ?? Auth::id(),
        ];
    }
}
