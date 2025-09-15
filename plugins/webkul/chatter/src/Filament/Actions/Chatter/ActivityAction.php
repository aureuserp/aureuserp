<?php

namespace Webkul\Chatter\Filament\Actions\Chatter;

use Exception;
use Filament\Actions\Action;
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
use Illuminate\Support\Carbon;
use Illuminate\Support\HtmlString;
use Webkul\Security\Models\User;
use Webkul\Support\Models\ActivityPlan;
use Webkul\Support\Models\ActivityType;

class ActivityAction extends Action
{
    protected mixed $activityPlans;

    public static function getDefaultName(): ?string
    {
        return 'activity.action';
    }

    public function setActivityPlans(mixed $activityPlans)
    {
        $this->activityPlans = $activityPlans;

        return $this;
    }

    public function getActivityPlans(): mixed
    {
        return $this->activityPlans;
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
                                        ->hidden($this->getActivityPlans()->isEmpty())
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
                                        ->hidden(fn (Get $get) => $get('activity_type_id') ? ActivityType::find($get('activity_type_id'))->category == 'meeting' : false)
                                        ->visible(fn (Get $get) => ! $get('activity_plan_id')),
                                    TextInput::make('summary')
                                        ->label(__('chatter::filament/resources/actions/chatter/activity-action.setup.form.fields.summary'))
                                        ->visible(fn (Get $get) => ! $get('activity_plan_id')),
                                    Select::make('assigned_to')
                                        ->label(__('chatter::filament/resources/actions/chatter/activity-action.setup.form.fields.assigned-to'))
                                        ->searchable()
                                        ->hidden(fn (Get $get) => $get('activity_type_id') ? ActivityType::find($get('activity_type_id'))->category == 'meeting' : false)
                                        ->live()
                                        ->visible(fn (Get $get) => ! $get('activity_plan_id'))
                                        ->options(User::all()->pluck('name', 'id')->toArray())
                                        ->required(),
                                ])->columns(2),
                            RichEditor::make('body')
                                ->hiddenLabel()
                                ->hidden(fn (Get $get) => $get('activity_type_id') ? ActivityType::find($get('activity_type_id'))->category == 'meeting' : false)
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
                    $user = filament()->auth()->user();

                    $data['assigned_to'] = $data['assigned_to'] ?? $user->id;

                    if (isset($data['activity_plan_id'])) {
                        $activityPlan = ActivityPlan::find($data['activity_plan_id']);

                        $body = "The plan \"{$activityPlan->name}\" has been started";

                        foreach ($activityPlan->activityPlanTemplates as $activityPlanTemplate) {
                            $data = [
                                ...$data,
                                ...$activityPlanTemplate->toArray(),
                                'body'        => $activityPlanTemplate['note'] ?? null,
                                'causer_type' => $user?->getMorphClass(),
                                'causer_id'   => $user->id,
                            ];

                            $body .= '<div class="space-y-2" style="margin-left: 20px;">
                                <div class="flex items-center space-x-2">
                                    <span>•</span>
                                    <span style="margin-left:2px;">'.
                                $activityPlanTemplate->summary.
                                ' ('.(isset($data['date_deadline']) ? $data['date_deadline'] : now()->format('m/d/Y')).')'.
                                '</span>
                                </div>
                            </div>';

                            $record->addMessage($data, $user->id);
                        }

                        $data['type'] = 'comment';
                        $data['body'] = $body;

                        $record->addMessage($data, $user->id);
                    } else {
                        $data['content'] = $activityPlanTemplate['note'] ?? null;
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
            ->slideOver(false);
    }
}
