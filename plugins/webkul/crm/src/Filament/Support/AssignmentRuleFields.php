<?php

namespace Webkul\Crm\Filament\Support;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Webkul\Crm\Enums\OpportunityPriority;
use Webkul\Crm\Models\Lead;
use Webkul\Crm\Models\Stage;
use Webkul\Crm\Models\Tag;
use Webkul\Crm\Services\AssignmentRules;
use Webkul\Support\Models\Country;
use Webkul\Support\Models\State;
use Webkul\Support\Models\UtmCampaign;
use Webkul\Support\Models\UTMMedium;
use Webkul\Support\Models\UTMSource;

class AssignmentRuleFields
{
    protected const CHOICE_MODELS = [
        'country_id'      => Country::class,
        'stage_id'        => Stage::class,
        'tag_ids'         => Tag::class,
        'utm_campaign_id' => UtmCampaign::class,
        'utm_medium_id'   => UTMMedium::class,
        'utm_source_id'   => UTMSource::class,
    ];

    public static function make(bool $framed = true): Fieldset|Group
    {
        $label = fn (string $key): string => __('crm::filament/assignment.rules.'.$key);

        $container = $framed ? Fieldset::make($label('title')) : Group::make();

        return $container
            ->statePath('assignment_domain')
            ->columns(1)
            ->schema([
                ToggleButtons::make('match')
                    ->label($label('match'))
                    ->options([
                        AssignmentRules::MATCH_ALL => $label('match-all'),
                        AssignmentRules::MATCH_ANY => $label('match-any'),
                    ])
                    ->default(AssignmentRules::MATCH_ALL)
                    ->afterStateHydrated(fn (ToggleButtons $component, ?string $state) => $component->state($state ?: AssignmentRules::MATCH_ALL))
                    ->inline()
                    ->grouped()
                    ->live(),
                Repeater::make('conditions')
                    ->label($label('conditions'))
                    ->hiddenLabel()
                    ->schema([
                        Select::make('field')
                            ->label($label('field'))
                            ->options(static::fieldOptions())
                            ->searchable()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Set $set, ?string $state): void {
                                $set('operator', AssignmentRules::operatorsFor($state)[0] ?? null);
                                $set('value', null);
                            }),
                        Select::make('operator')
                            ->label($label('operator'))
                            ->options(fn (Get $get): array => collect(AssignmentRules::operatorsFor($get('field')))
                                ->mapWithKeys(fn (string $operator): array => [$operator => static::operatorLabel($get('field'), $operator)])
                                ->all())
                            ->selectablePlaceholder(false)
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Set $set, ?string $state): void {
                                if (in_array($state, AssignmentRules::VALUELESS_OPERATORS, true)) {
                                    $set('value', null);
                                }
                            }),
                        Group::make(fn (Get $get): array => array_filter([static::valueField($get('field'), $get('operator'))])),
                    ])
                    ->columns(3)
                    ->defaultItems(0)
                    ->reorderable(false)
                    ->addActionLabel($label('add-condition'))
                    ->live(),
                Text::make(fn (Get $get): string => static::matchingSummary([
                    'match'      => $get('match'),
                    'conditions' => $get('conditions'),
                ])),
            ]);
    }

    public static function matchingSummary(array $rules): string
    {
        if (blank(AssignmentRules::clean($rules))) {
            return __('crm::filament/assignment.rules.no-conditions');
        }

        $count = AssignmentRules::apply(Lead::query()->open(), $rules)->count();

        return trans_choice('crm::filament/assignment.rules.matching', $count, ['count' => $count]);
    }

    public static function fieldOptions(): array
    {
        return collect(array_keys(AssignmentRules::FIELDS))
            ->mapWithKeys(fn (string $field): array => [$field => __('crm::filament/assignment.rules.fields.'.$field)])
            ->sort()
            ->all();
    }

    public static function operatorLabel(?string $field, string $operator): string
    {
        $type = AssignmentRules::fieldType($field);

        return __('crm::filament/assignment.rules.operators.'.($type === 'date' ? 'date.' : '').$operator);
    }

    protected static function valueField(?string $field, ?string $operator): ?Field
    {
        $type = AssignmentRules::fieldType($field);

        if (! $type || ! $operator || in_array($operator, AssignmentRules::VALUELESS_OPERATORS, true)) {
            return null;
        }

        $label = __('crm::filament/assignment.rules.value');

        if ($field === 'priority') {
            return Select::make('value')
                ->label($label)
                ->options(collect(OpportunityPriority::cases())->mapWithKeys(fn (OpportunityPriority $priority): array => [$priority->value => $priority->getLabel()])->all())
                ->required()
                ->live();
        }

        return match ($type) {
            'text' => TextInput::make('value')
                ->label($label)
                ->required()
                ->live(debounce: 500),
            'number' => TextInput::make('value')
                ->label($label)
                ->numeric()
                ->required()
                ->live(debounce: 500),
            'date' => DatePicker::make('value')
                ->label($label)
                ->native(false)
                ->required()
                ->live(),
            'choice', 'tags' => $field === 'state_id'
                ? Select::make('value')
                    ->label($label)
                    ->multiple()
                    ->searchable()
                    ->getSearchResultsUsing(fn (string $search): array => static::stateLabels(State::query()->where('name', 'like', '%'.$search.'%')->limit(50)))
                    ->getOptionLabelsUsing(fn (array $values): array => static::stateLabels(State::query()->whereKey($values)))
                    ->required()
                    ->live()
                : Select::make('value')
                    ->label($label)
                    ->multiple()
                    ->searchable()
                    ->options(fn (): array => static::choiceOptions($field))
                    ->required()
                    ->live(),
        };
    }

    protected static function choiceOptions(string $field): array
    {
        if ($field === 'type') {
            return [
                Lead::TYPE_LEAD        => __('crm::models/lead.title'),
                Lead::TYPE_OPPORTUNITY => __('crm::models/opportunity.title'),
            ];
        }

        $model = self::CHOICE_MODELS[$field];

        return $model::query()->orderBy('name')->pluck('name', 'id')->all();
    }

    protected static function stateLabels(Builder $query): array
    {
        return $query->with('country')->orderBy('name')->get()
            ->mapWithKeys(fn (State $state): array => [$state->getKey() => $state->name.' ('.$state->country?->code.')'])
            ->all();
    }
}
