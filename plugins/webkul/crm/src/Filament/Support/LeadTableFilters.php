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
        $label = fn (string $key): string => __('crm::filament/clusters/leads/resources/lead.'.$key);
        $opportunityLabel = fn (string $key): string => __('crm::filament/clusters/sales/resources/opportunity.table.columns.'.$key);

        return static::make([
            TextConstraint::make('name')->label($label('table.columns.name'))->icon('heroicon-o-document-text'),
            TextConstraint::make('contact_name')->label($label('table.columns.contact-name'))->icon('heroicon-o-user'),
            TextConstraint::make('company_name')->label($label('table.columns.company-name'))->icon('heroicon-o-building-office'),
            TextConstraint::make('email')->label($label('table.columns.email'))->icon('heroicon-o-envelope'),
            TextConstraint::make('phone')->label($label('table.columns.phone'))->icon('heroicon-o-phone'),
            TextConstraint::make('city')->label($label('table.columns.city'))->icon('heroicon-o-map-pin'),
            static::relationship('country', $label('table.columns.country'), 'heroicon-o-globe-alt'),
            static::relationship('stage', $opportunityLabel('stage'), 'heroicon-o-funnel'),
            static::relationship('user', $label('table.columns.salesperson'), 'heroicon-o-user-circle'),
            static::relationship('team', $label('table.columns.team'), 'heroicon-o-user-group', static::companyTeams()),
            static::relationship('tags', $label('table.columns.tags'), 'heroicon-o-tag'),
            static::priority($label('table.columns.priority')),
            static::relationship('source', $label('table.columns.source'), 'heroicon-o-speaker-wave'),
            static::relationship('medium', $label('form.fields.medium'), 'heroicon-o-megaphone'),
            static::relationship('campaign', $label('form.fields.campaign'), 'heroicon-o-flag'),
            NumberConstraint::make('probability')->label($opportunityLabel('probability'))->icon('heroicon-o-chart-pie'),
            DateConstraint::make('created_at')->label($label('table.columns.created-at')),
        ]);
    }

    public static function opportunities(bool $withinTeam = false): QueryBuilder
    {
        $label = fn (string $key): string => __('crm::filament/clusters/sales/resources/opportunity.table.columns.'.$key);
        $leadLabel = fn (string $key): string => __('crm::filament/clusters/leads/resources/lead.'.$key);

        return static::make([
            TextConstraint::make('name')->label($label('name'))->icon('heroicon-o-document-text'),
            static::relationship('partner', $label('customer'), 'heroicon-o-building-office'),
            TextConstraint::make('email')->label($label('email'))->icon('heroicon-o-envelope'),
            TextConstraint::make('phone')->label($label('phone'))->icon('heroicon-o-phone'),
            static::relationship('stage', $label('stage'), 'heroicon-o-funnel'),
            static::relationship('user', $label('salesperson'), 'heroicon-o-user-circle'),
            $withinTeam ? null : static::relationship('team', $label('team'), 'heroicon-o-user-group', static::companyTeams()),
            static::relationship('tags', $label('tags'), 'heroicon-o-tag'),
            static::priority($label('priority')),
            static::relationship('source', $leadLabel('table.columns.source'), 'heroicon-o-speaker-wave'),
            static::relationship('medium', $leadLabel('form.fields.medium'), 'heroicon-o-megaphone'),
            static::relationship('campaign', $leadLabel('form.fields.campaign'), 'heroicon-o-flag'),
            static::relationship('country', $leadLabel('table.columns.country'), 'heroicon-o-globe-alt'),
            static::relationship('lostReason', $label('lost-reason'), 'heroicon-o-hand-thumb-down'),
            NumberConstraint::make('expected_revenue')->label($label('expected-revenue'))->icon('heroicon-o-banknotes'),
            NumberConstraint::make('probability')->label($label('probability'))->icon('heroicon-o-chart-pie'),
            DateConstraint::make('expected_closing_date')->label($label('expected-closing')),
            DateConstraint::make('created_at')->label($label('created-at')),
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
