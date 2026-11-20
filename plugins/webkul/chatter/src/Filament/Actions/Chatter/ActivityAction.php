<?php

namespace Webkul\Chatter\Filament\Actions\Chatter;

use Exception;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use Webkul\Chatter\Support\ChatterMentions;
use Webkul\Security\Models\User;
use Webkul\Support\Models\ActivityPlan;
use Webkul\Support\Models\ActivityType;

class ActivityAction extends Action
{
    protected mixed $activityPlans = null;

    public static function getDefaultName(): ?string
    {
        return 'activity.action';
    }

    public function setActivityPlans(mixed $activityPlans)
    {
        $this->activityPlans = $activityPlans;

        return $this;
    }

    public function getActivityPlans(): Collection
    {
        return ActivityPlan::withTemplates($this->resolveActivityPlans());
    }

    protected function resolveActivityPlans(): Collection
    {
        if ($this->activityPlans instanceof Collection) {
            return $this->activityPlans;
        }

        if (is_array($this->activityPlans)) {
            return collect($this->activityPlans);
        }

        $record = $this->getRecord();

        if (($record instanceof Model) && method_exists($record, 'activityPlans')) {
            $plans = $record->activityPlans();

            if ($plans instanceof Collection) {
                return $plans;
            }

            if (is_array($plans)) {
                return collect($plans);
            }
        }

        return collect();
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->color('gray')
            ->outlined()
            ->schema(function ($form, $record) {
                return $form->schema([
                    Group::make()
                        ->schema([
                            Group::make()
                                ->schema([
                                    Select::make('activity_plan_id')
                                        ->label(__('chatter::filament/resources/actions/chatter/activity-action.setup.form.fields.activity-plan'))
                                        ->options($this->getActivityPlans())
                                        ->searchable()
                                        ->hidden(fn (): bool => $this->getActivityPlans()->isEmpty())
                                        ->preload()
                                        ->live(),
                                    DatePicker::make('date_deadline')
                                        ->label(__('chatter::filament/resources/actions/chatter/activity-action.setup.form.fields.plan-date'))
                                        ->hidden(fn (Get $get) => ! $get('activity_plan_id'))
                                        ->live()
                                        ->native(false),
                                ])
                                ->columns(2),

                            Group::make()
                                ->schema([
                                    TextEntry::make('plan_summary')
                                        ->label(__('chatter::filament/resources/actions/chatter/activity-action.setup.form.fields.plan-summary'))
                                        ->state(function (Get $get) {
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
                                        ->label(__('chatter::filament/resources/actions/chatter/activity-action.setup.form.fields.activity-type'))
                                        ->options(ActivityType::pluck('name', 'id'))
                                        ->searchable()
                                        ->preload()
                                        ->live()
                                        ->required()
                                        ->visible(fn (Get $get) => ! $get('activity_plan_id')),
                                    DatePicker::make('date_deadline')
                                        ->label(__('chatter::filament/resources/actions/chatter/activity-action.setup.form.fields.due-date'))
                                        ->native(false)
                                        ->visible(fn (Get $get) => ! $get('activity_plan_id')),
                                    TextInput::make('summary')
                                        ->label(__('chatter::filament/resources/actions/chatter/activity-action.setup.form.fields.summary'))
                                        ->visible(fn (Get $get) => ! $get('activity_plan_id')),
                                    Select::make('assigned_to')
                                        ->label(__('chatter::filament/resources/actions/chatter/activity-action.setup.form.fields.assigned-to'))
                                        ->searchable()
                                        ->live()
                                        ->visible(fn (Get $get) => ! $get('activity_plan_id'))
                                        ->options(User::all()->pluck('name', 'id')->toArray())
                                        ->required(),
                                ])->columns(2),
                            RichEditor::make('body')
                                ->hiddenLabel()
                                ->mentions([ChatterMentions::provider()])
                                ->visible(fn (Get $get) => ! $get('activity_plan_id'))
                                ->placeholder(__('chatter::filament/resources/actions/chatter/activity-action.setup.form.fields.log-note'))
                                ->visible(fn (Get $get) => ! $get('activity_plan_id')),
                            Hidden::make('type')
                                ->default('activity'),
                        ]),
                ]);
            })
            ->action(function (array $data, ?Model $record = null) {
                try {
                    $user = Filament::auth()->user() ?? Auth::user();

                    $data['assigned_to'] = $data['assigned_to'] ?? $user->id;

                    $activityPlanId = $data['activity_plan_id'] ?? null;

                    $data = Arr::except($data, ['activity_plan_id']);

                    if ($activityPlanId) {
                        $record->scheduleActivityPlan(ActivityPlan::find($activityPlanId), [
                            'date_deadline' => $data['date_deadline'] ?? null,
                            'assigned_to'   => $data['assigned_to'],
                        ]);
                    } else {
                        $data['causer_type'] = $user?->getMorphClass();
                        $data['causer_id'] = $user->id;

                        $record->addMessage($data, $user->id);
                    }

                    Notification::make()
                        ->success()
                        ->title(__('chatter::filament/resources/actions/chatter/activity-action.setup.actions.notification.success.title'))
                        ->body(__('chatter::filament/resources/actions/chatter/activity-action.setup.actions.notification.success.body'))
                        ->send();
                } catch (Exception $e) {
                    Notification::make()
                        ->danger()
                        ->title(__('chatter::filament/resources/actions/chatter/activity-action.setup.actions.notification.error.title'))
                        ->body(__('chatter::filament/resources/actions/chatter/activity-action.setup.actions.notification.error.body'))
                        ->send();

                    report($e);
                }
            })
            ->after(function ($livewire) {
                if (method_exists($livewire, 'dispatch')) {
                    $livewire->dispatch('chatter.refresh');
                }
            })
            ->label(__('chatter::filament/resources/actions/chatter/activity-action.setup.title'))
            ->icon('heroicon-o-clock')
            ->modalIcon('heroicon-o-clock')
            ->modalSubmitAction(function ($action) {
                $action->label(__('chatter::filament/resources/actions/chatter/activity-action.setup.submit-action-title'));
                $action->icon('heroicon-m-paper-airplane');
            })
            ->slideOver(false)
            ->extraModalWindowAttributes(['class' => 'activity-modal fi-chatter-modal']);
    }
}
