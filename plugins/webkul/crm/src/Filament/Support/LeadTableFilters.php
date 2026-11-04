<?php

namespace Webkul\Crm\Filament\Support;

use Closure;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\Constraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\NumberConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\Operators\Operator;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\Tables\Filters\QueryBuilder\Constraints\SelectConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Crm\Enums\LeadType;
use Webkul\Crm\Enums\OpportunityPriority;

class LeadTableFilters
{
    public static function leads(): QueryBuilder
    {
        return static::make([
            TextConstraint::make('name')->label(__('crm::filament/clusters/leads/resources/lead.table.columns.name'))->icon('heroicon-o-document-text'),
            TextConstraint::make('contact_name')->label(__('crm::filament/clusters/leads/resources/lead.table.columns.contact-name'))->icon('heroicon-o-user'),
            TextConstraint::make('company_name')->label(__('crm::filament/clusters/leads/resources/lead.table.columns.company-name'))->icon('heroicon-o-building-office'),
            TextConstraint::make('email')->label(__('crm::filament/clusters/leads/resources/lead.table.columns.email'))->icon('heroicon-o-envelope'),
            TextConstraint::make('phone')->label(__('crm::filament/clusters/leads/resources/lead.table.columns.phone'))->icon('heroicon-o-phone'),
            TextConstraint::make('city')->label(__('crm::filament/clusters/leads/resources/lead.table.columns.city'))->icon('heroicon-o-map-pin'),
            static::relationship('country', __('crm::filament/clusters/leads/resources/lead.table.columns.country'), 'heroicon-o-globe-alt'),
            static::relationship('stage', __('crm::filament/clusters/sales/resources/opportunity.table.columns.stage'), 'heroicon-o-funnel'),
            static::relationship('user', __('crm::filament/clusters/leads/resources/lead.table.columns.salesperson'), 'heroicon-o-user-circle'),
            static::relationship('team', __('crm::filament/clusters/leads/resources/lead.table.columns.team'), 'heroicon-o-user-group', static::companyTeams()),
            static::relationship('tags', __('crm::filament/clusters/leads/resources/lead.table.columns.tags'), 'heroicon-o-tag'),
            static::priority(__('crm::filament/clusters/leads/resources/lead.table.columns.priority')),
            static::relationship('source', __('crm::filament/clusters/leads/resources/lead.table.columns.source'), 'heroicon-o-speaker-wave'),
            static::relationship('medium', __('crm::filament/clusters/leads/resources/lead.form.fields.medium'), 'heroicon-o-megaphone'),
            static::relationship('campaign', __('crm::filament/clusters/leads/resources/lead.form.fields.campaign'), 'heroicon-o-flag'),
            NumberConstraint::make('probability')->label(__('crm::filament/clusters/sales/resources/opportunity.table.columns.probability'))->icon('heroicon-o-chart-pie'),
            DateConstraint::make('created_at')->label(__('crm::filament/clusters/leads/resources/lead.table.columns.created-at')),
        ]);
    }

    public static function opportunities(): QueryBuilder
    {
        return static::make([
            TextConstraint::make('name')->label(__('crm::filament/clusters/sales/resources/opportunity.table.columns.name'))->icon('heroicon-o-document-text'),
            static::relationship('partner', __('crm::filament/clusters/sales/resources/opportunity.table.columns.customer'), 'heroicon-o-building-office'),
            TextConstraint::make('email')->label(__('crm::filament/clusters/sales/resources/opportunity.table.columns.email'))->icon('heroicon-o-envelope'),
            TextConstraint::make('phone')->label(__('crm::filament/clusters/sales/resources/opportunity.table.columns.phone'))->icon('heroicon-o-phone'),
            static::relationship('stage', __('crm::filament/clusters/sales/resources/opportunity.table.columns.stage'), 'heroicon-o-funnel'),
            static::relationship('user', __('crm::filament/clusters/sales/resources/opportunity.table.columns.salesperson'), 'heroicon-o-user-circle'),
            static::relationship('team', __('crm::filament/clusters/sales/resources/opportunity.table.columns.team'), 'heroicon-o-user-group', static::companyTeams()),
            static::relationship('tags', __('crm::filament/clusters/sales/resources/opportunity.table.columns.tags'), 'heroicon-o-tag'),
            static::priority(__('crm::filament/clusters/sales/resources/opportunity.table.columns.priority')),
            static::relationship('source', __('crm::filament/clusters/leads/resources/lead.table.columns.source'), 'heroicon-o-speaker-wave'),
            static::relationship('medium', __('crm::filament/clusters/leads/resources/lead.form.fields.medium'), 'heroicon-o-megaphone'),
            static::relationship('campaign', __('crm::filament/clusters/leads/resources/lead.form.fields.campaign'), 'heroicon-o-flag'),
            static::relationship('country', __('crm::filament/clusters/leads/resources/lead.table.columns.country'), 'heroicon-o-globe-alt'),
            static::relationship('lostReason', __('crm::filament/clusters/sales/resources/opportunity.table.columns.lost-reason'), 'heroicon-o-hand-thumb-down'),
            NumberConstraint::make('expected_revenue')->label(__('crm::filament/clusters/sales/resources/opportunity.table.columns.expected-revenue'))->icon('heroicon-o-banknotes'),
            NumberConstraint::make('probability')->label(__('crm::filament/clusters/sales/resources/opportunity.table.columns.probability'))->icon('heroicon-o-chart-pie'),
            DateConstraint::make('expected_closing_date')->label(__('crm::filament/clusters/sales/resources/opportunity.table.columns.expected-closing')),
            DateConstraint::make('created_at')->label(__('crm::filament/clusters/sales/resources/opportunity.table.columns.created-at')),
        ]);
    }

    public static function teamRule(int|string $teamId): array
    {
        return ['type' => 'team', 'data' => ['operator' => 'isRelatedTo', 'settings' => ['values' => [(string) $teamId]]]];
    }

    public static function activityLogs(): QueryBuilder
    {
        return static::make([
            static::relationship('activityType', __('crm::filament/clusters/reporting/report.dimensions.activity-type'), 'heroicon-o-clipboard-document-list'),
            static::relationship('assignedTo', __('crm::filament/clusters/reporting/report.dimensions.assigned-to'), 'heroicon-o-user'),
            static::relationship('doneBy', __('crm::filament/clusters/reporting/report.dimensions.done-by'), 'heroicon-o-user-circle'),
            static::leadType('lead_type'),
            DateConstraint::make('done_at')->label(__('crm::filament/clusters/reporting/report.dimensions.done-at')),
        ]);
    }

    public static function report(QueryBuilder $filter, array $defaultRules, array $constraints = []): QueryBuilder
    {
        return $filter
            ->constraints([
                static::relationship('user', __('crm::filament/clusters/reporting/report.dimensions.salesperson'), 'heroicon-o-user-circle')->emptyable(),
                ...$constraints,
                static::active(),
                static::won(),
                DateConstraint::make('closed_at')->label(__('crm::filament/clusters/reporting/report.dimensions.closed-at')),
            ])
            ->default($defaultRules);
    }

    public static function activeRule(): array
    {
        return ['type' => 'active', 'data' => ['operator' => 'isActive', 'settings' => []]];
    }

    public static function salespersonRule(int|string $userId): array
    {
        return ['type' => 'user', 'data' => ['operator' => 'isRelatedTo', 'settings' => ['values' => [(string) $userId]]]];
    }

    public static function relativeDateRule(string $field, string $preset): array
    {
        return ['type' => $field, 'data' => ['operator' => 'isAfter', 'settings' => ['mode' => 'relative', 'preset' => $preset]]];
    }

    public static function leadType(string $column): SelectConstraint
    {
        return SelectConstraint::make($column)
            ->label(__('crm::filament/clusters/reporting/report.dimensions.type'))
            ->icon('heroicon-o-squares-2x2')
            ->options(LeadType::class)
            ->multiple();
    }

    public static function lost(): Constraint
    {
        return Constraint::make('lost')
            ->label(__('crm::filament/clusters/reporting/report.rules.lost.label'))
            ->icon('heroicon-o-hand-thumb-down')
            ->attribute('deleted_at')
            ->operators([IsLostOperator::class]);
    }

    public static function won(): Constraint
    {
        $wonStage = fn (Builder $query): Builder => $query->where('is_won', true);

        return Constraint::make('won')
            ->label(__('crm::filament/clusters/reporting/report.rules.won.label'))
            ->icon('heroicon-o-trophy')
            ->operators([
                Operator::make('isWon')
                    ->label(fn (bool $isInverse): string => $isInverse
                        ? __('crm::filament/clusters/reporting/report.rules.won.is-not-won')
                        : __('crm::filament/clusters/reporting/report.rules.won.is-won'))
                    ->summary(fn (bool $isInverse): string => $isInverse
                        ? __('crm::filament/clusters/reporting/report.rules.won.not-won')
                        : __('crm::filament/clusters/reporting/report.rules.won.label'))
                    ->query(fn (Builder $query, bool $isInverse): Builder => $isInverse
                        ? $query->whereDoesntHave('stage', $wonStage)
                        : $query->whereHas('stage', $wonStage)),
            ]);
    }

    protected static function active(): Constraint
    {
        return Constraint::make('active')
            ->label(__('crm::filament/clusters/reporting/report.rules.active.label'))
            ->icon('heroicon-o-check-circle')
            ->attribute('deleted_at')
            ->operators([
                Operator::make('isActive')
                    ->label(fn (bool $isInverse): string => $isInverse
                        ? __('crm::filament/clusters/reporting/report.rules.active.is-not-active')
                        : __('crm::filament/clusters/reporting/report.rules.active.is-active'))
                    ->summary(fn (bool $isInverse): string => $isInverse
                        ? __('crm::filament/clusters/reporting/report.rules.active.not-active')
                        : __('crm::filament/clusters/reporting/report.rules.active.label'))
                    ->query(fn (Builder $query, string $qualifiedColumn, bool $isInverse): Builder => $isInverse
                        ? $query->whereNotNull($qualifiedColumn)
                        : $query->whereNull($qualifiedColumn)),
            ]);
    }

    public static function make(array $constraints): QueryBuilder
    {
        return DefaultRulesQueryBuilder::make()
            ->constraintPickerColumns(2)
            ->constraints(array_values(array_filter($constraints)));
    }

    public static function relationship(string $relationship, string $label, string $icon, ?Closure $modifyQuery = null): RelationshipConstraint
    {
        return RelationshipConstraint::make($relationship)
            ->label($label)
            ->icon($icon)
            ->multiple()
            ->selectable(
                IsRelatedToOperator::make()
                    ->titleAttribute('name')
                    ->label($label)
                    ->searchable()
                    ->multiple()
                    ->preload()
                    ->modifyRelationshipQueryUsing($modifyQuery),
            );
    }

    protected static function companyTeams(): Closure
    {
        return fn (Builder $query): Builder => $query->where(owned_by_company(current_company_id()));
    }

    protected static function priority(string $label): SelectConstraint
    {
        return SelectConstraint::make('priority')
            ->label($label)
            ->icon('heroicon-o-star')
            ->options(OpportunityPriority::class)
            ->multiple();
    }
}
