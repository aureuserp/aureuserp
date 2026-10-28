<?php

namespace Webkul\Crm\Filament\Support;

use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Crm\Enums\AssignmentRuleMatch;
use Webkul\Crm\Enums\AssignmentRuleOperator;
use Webkul\Crm\Enums\LeadType;
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

    public static function make(bool $framed = true, ?Closure $scope = null): Fieldset|Group
    {
        $container = $framed ? Fieldset::make(__('crm::filament/assignment.rules.title')) : Group::make();

        return $container
            ->statePath('assignment_domain')
            ->columns(1)
            ->schema([
                Hidden::make('match')
                    ->default(AssignmentRuleMatch::ALL->value),
                Text::make(fn (Get $get): string => $get('match') === AssignmentRuleMatch::ANY->value
                    ? __('crm::filament/assignment.rules.heading-any')
                    : __('crm::filament/assignment.rules.heading-all')),
                Repeater::make('conditions')
                    ->label(__('crm::filament/assignment.rules.conditions'))
                    ->hiddenLabel()
                    ->table([
                        TableColumn::make(__('crm::filament/assignment.rules.field')),
                        TableColumn::make(__('crm::filament/assignment.rules.operator')),
                        TableColumn::make(__('crm::filament/assignment.rules.value')),
                    ])
                    ->schema([
                        Select::make('field')
                            ->label(__('crm::filament/assignment.rules.field'))
                            ->hiddenLabel()
                            ->options(static::fieldOptions())
                            ->searchable()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Set $set, ?string $state): void {
                                $set('operator', (AssignmentRules::operatorsFor($state)[0] ?? null)?->value);
                                $set('value', null);
                            }),
                        Select::make('operator')
                            ->label(__('crm::filament/assignment.rules.operator'))
                            ->hiddenLabel()
                            ->options(fn (Get $get): array => collect(AssignmentRules::operatorsFor($get('field')))
                                ->mapWithKeys(fn (AssignmentRuleOperator $operator): array => [$operator->value => static::operatorLabel($get('field'), $operator)])
                                ->all())
                            ->selectablePlaceholder(false)
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Set $set, ?string $state): void {
                                if (AssignmentRuleOperator::tryFrom((string) $state)?->isValueless()) {
                                    $set('value', null);
                                }
                            }),
                        Group::make(fn (Get $get): array => array_filter([static::valueField($get('field'), $get('operator'))?->hiddenLabel()])),
                    ])
                    ->defaultItems(0)
                    ->reorderable(false)
                    ->addActionLabel(__('crm::filament/assignment.rules.add-condition'))
                    ->addAction(fn (Action $action): Action => $action->link()->icon('heroicon-m-plus'))
                    ->deleteAction(fn (Action $action): Action => $action->color('gray')->icon('heroicon-o-trash'))
                    ->extraAttributes(['class' => 'crm-rule-rows'])
                    ->live(),
                Actions::make([
                    Action::make('matchingRecords')
                        ->label(fn (Get $get): string => static::matchingSummary(['match' => $get('match'), 'conditions' => $get('conditions')], $scope))
                        ->icon('heroicon-m-arrow-right')
                        ->link()
                        ->modalHeading(__('crm::filament/assignment.rules.records-modal.heading'))
                        ->modalDescription(__('crm::filament/assignment.rules.records-modal.description'))
                        ->modalContent(fn (Get $get) => view('crm::filament.assignment.matching-records', static::matchingRecords(['match' => $get('match'), 'conditions' => $get('conditions')], $scope)))
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel(__('crm::filament/assignment.rules.records-modal.close'))
                        ->modalWidth('5xl'),
                ]),
            ]);
    }

    public static function matchingQuery(array $rules, ?Closure $scope = null): Builder
    {
        $query = Lead::query()->open();

        if ($scope) {
            $scope($query);
        }

        return AssignmentRules::apply($query, $rules);
    }

    public static function unassignedToTeam(): Closure
    {
        return fn (Builder $query): Builder => $query->whereNull('team_id');
    }

    public static function unassignedInTeam(Closure $teamId): Closure
    {
        return fn (Builder $query): Builder => $query
            ->whereNull('user_id')
            ->where(fn (Builder $query): Builder => $query
                ->whereNull('team_id')
                ->when($teamId(), fn (Builder $query, int $id): Builder => $query->orWhere('team_id', $id)));
    }

    public static function matchingRecords(array $rules, ?Closure $scope = null, int $limit = 50): array
    {
        $query = static::matchingQuery($rules, $scope);

        return [
            'total'   => (clone $query)->count(),
            'records' => $query->with(['user', 'stage', 'partner', 'company.currency'])->latest()->limit($limit)->get(),
        ];
    }

    public static function matchingSummary(array $rules, ?Closure $scope = null): string
    {
        $counts = static::matchingQuery($rules, $scope)
            ->reorder()
            ->selectRaw('type, count(*) as aggregate')
            ->groupBy('type')
            ->pluck('aggregate', 'type');

        $leads = (int) ($counts[LeadType::LEAD->value] ?? 0);
        $opportunities = (int) ($counts[LeadType::OPPORTUNITY->value] ?? 0);

        $total = $leads + $opportunities;

        return trans_choice('crm::filament/assignment.rules.records', $total, ['count' => $total])
            .($total > 0 ? ' ('.__('crm::filament/assignment.rules.breakdown', ['leads' => $leads, 'opportunities' => $opportunities]).')' : '');
    }

    public static function fieldOptions(): array
    {
        return collect([
            'type'                  => __('crm::filament/assignment.rules.fields.type'),
            'name'                  => __('crm::filament/assignment.rules.fields.name'),
            'contact_name'          => __('crm::filament/assignment.rules.fields.contact_name'),
            'company_name'          => __('crm::filament/assignment.rules.fields.company_name'),
            'job_title'             => __('crm::filament/assignment.rules.fields.job_title'),
            'email'                 => __('crm::filament/assignment.rules.fields.email'),
            'phone'                 => __('crm::filament/assignment.rules.fields.phone'),
            'website'               => __('crm::filament/assignment.rules.fields.website'),
            'street1'               => __('crm::filament/assignment.rules.fields.street1'),
            'city'                  => __('crm::filament/assignment.rules.fields.city'),
            'zip'                   => __('crm::filament/assignment.rules.fields.zip'),
            'state_id'              => __('crm::filament/assignment.rules.fields.state_id'),
            'country_id'            => __('crm::filament/assignment.rules.fields.country_id'),
            'stage_id'              => __('crm::filament/assignment.rules.fields.stage_id'),
            'tag_ids'               => __('crm::filament/assignment.rules.fields.tag_ids'),
            'priority'              => __('crm::filament/assignment.rules.fields.priority'),
            'probability'           => __('crm::filament/assignment.rules.fields.probability'),
            'expected_revenue'      => __('crm::filament/assignment.rules.fields.expected_revenue'),
            'expected_closing_date' => __('crm::filament/assignment.rules.fields.expected_closing_date'),
            'created_at'            => __('crm::filament/assignment.rules.fields.created_at'),
            'utm_campaign_id'       => __('crm::filament/assignment.rules.fields.utm_campaign_id'),
            'utm_medium_id'         => __('crm::filament/assignment.rules.fields.utm_medium_id'),
            'utm_source_id'         => __('crm::filament/assignment.rules.fields.utm_source_id'),
            'referred_by'           => __('crm::filament/assignment.rules.fields.referred_by'),
        ])
            ->sort()
            ->all();
    }

    public static function operatorLabel(?string $field, AssignmentRuleOperator $operator): string
    {
        return AssignmentRules::fieldType($field) === 'date' ? $operator->getDateLabel() : $operator->getLabel();
    }

    protected static function valueField(?string $field, ?string $operator): ?Field
    {
        $type = AssignmentRules::fieldType($field);

        if (! $type
            || ! $operator
            || AssignmentRuleOperator::tryFrom($operator)?->isValueless() !== false) {
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
            return LeadType::options();
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
