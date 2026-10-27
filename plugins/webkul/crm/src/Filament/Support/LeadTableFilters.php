<?php

namespace Webkul\Crm\Filament\Support;

use Closure;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\NumberConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\Tables\Filters\QueryBuilder\Constraints\SelectConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint;
use Illuminate\Database\Eloquent\Builder;
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

    public static function opportunities(bool $withinTeam = false): QueryBuilder
    {
        return static::make([
            TextConstraint::make('name')->label(__('crm::filament/clusters/sales/resources/opportunity.table.columns.name'))->icon('heroicon-o-document-text'),
            static::relationship('partner', __('crm::filament/clusters/sales/resources/opportunity.table.columns.customer'), 'heroicon-o-building-office'),
            TextConstraint::make('email')->label(__('crm::filament/clusters/sales/resources/opportunity.table.columns.email'))->icon('heroicon-o-envelope'),
            TextConstraint::make('phone')->label(__('crm::filament/clusters/sales/resources/opportunity.table.columns.phone'))->icon('heroicon-o-phone'),
            static::relationship('stage', __('crm::filament/clusters/sales/resources/opportunity.table.columns.stage'), 'heroicon-o-funnel'),
            static::relationship('user', __('crm::filament/clusters/sales/resources/opportunity.table.columns.salesperson'), 'heroicon-o-user-circle'),
            $withinTeam ? null : static::relationship('team', __('crm::filament/clusters/sales/resources/opportunity.table.columns.team'), 'heroicon-o-user-group', static::companyTeams()),
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

    public static function make(array $constraints): QueryBuilder
    {
        return QueryBuilder::make()
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
