<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Crm\Enums\OpportunityPriority;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\RecurringPlanResource;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\TagResource\Schemas\TagForm;
use Webkul\Crm\Models\RecurringPlan;
use Webkul\Crm\Models\Stage;
use Webkul\Field\Filament\Forms\Components\ProgressStepper as FormProgressStepper;
use Webkul\Partner\Models\Partner;

class OpportunityForm
{
    public static function configure(Schema $schema, bool $withinTeam = false): Schema
    {
        return $schema
            ->components([
                FormProgressStepper::make('stage_id')
                    ->label(__('crm::filament/clusters/sales/resources/opportunity.table.columns.stage'))
                    ->hiddenLabel()
                    ->inline()
                    ->extraAttributes(['class' => 'crm-stepper-previous-active'])
                    ->required()
                    ->options(fn (): array => Stage::query()->orderBy('sort')->pluck('name', 'id')->all())
                    ->default(fn (): ?int => Stage::query()->orderBy('sort')->value('id'))
                    ->columnSpanFull(),

                Group::make()
                    ->schema([
                        Section::make(__('crm::filament/clusters/sales/resources/opportunity.form.sections.general.title'))
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('crm::filament/clusters/sales/resources/opportunity.form.sections.general.fields.name'))
                                    ->placeholder(__('crm::filament/clusters/sales/resources/opportunity.form.sections.general.fields.name-placeholder'))
                                    ->required()
                                    ->maxLength(255)
                                    ->autofocus()
                                    ->extraInputAttributes(['style' => 'font-size: 1.5rem;height: 3rem;'])
                                    ->columnSpanFull(),

                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('expected_revenue')
                                            ->label(__('crm::filament/clusters/sales/resources/opportunity.form.sections.general.fields.expected-revenue'))
                                            ->numeric()
                                            ->minValue(0)
                                            ->default(0),
                                        TextInput::make('probability')
                                            ->label(__('crm::filament/clusters/sales/resources/opportunity.form.sections.general.fields.probability'))
                                            ->numeric()
                                            ->minValue(0)
                                            ->maxValue(100)
                                            ->suffix('%')
                                            ->default(10),
                                        TextInput::make('recurring_revenue')
                                            ->label(__('crm::filament/clusters/configurations/resources/recurring-plan.opportunity.recurring-revenue'))
                                            ->numeric()
                                            ->minValue(0)
                                            ->default(0)
                                            ->live(onBlur: true)
                                            ->visible(fn (): bool => RecurringPlanResource::isRecurringRevenueEnabled()),
                                        Select::make('recurring_plan_id')
                                            ->label(__('crm::filament/clusters/configurations/resources/recurring-plan.opportunity.plan'))
                                            ->options(fn (): array => RecurringPlan::query()->orderBy('sort')->pluck('name', 'id')->all())
                                            ->required(fn (Get $get): bool => (float) $get('recurring_revenue') > 0)
                                            ->visible(fn (): bool => RecurringPlanResource::isRecurringRevenueEnabled()),
                                    ]),

                                Select::make('partner_id')
                                    ->label(__('crm::filament/clusters/sales/resources/opportunity.form.sections.general.fields.customer'))
                                    ->relationship('partner', 'name', modifyQueryUsing: fn (Builder $query, Get $get): Builder => $query->where(owned_by_company($get('company_id'))))
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->afterStateUpdated(function (Set $set, mixed $state): void {
                                        $partner = $state ? Partner::query()->find($state) : null;

                                        $set('email', $partner?->email);

                                        $set('phone', $partner?->phone);
                                    }),

                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('email')
                                            ->label(__('crm::filament/clusters/sales/resources/opportunity.form.sections.general.fields.email'))
                                            ->email()
                                            ->maxLength(255),
                                        TextInput::make('phone')
                                            ->label(__('crm::filament/clusters/sales/resources/opportunity.form.sections.general.fields.phone'))
                                            ->tel()
                                            ->maxLength(255),
                                    ]),
                            ]),

                        Section::make(__('crm::filament/clusters/sales/resources/opportunity.form.sections.notes.title'))
                            ->schema([
                                RichEditor::make('description')
                                    ->label(__('crm::filament/clusters/leads/resources/lead.form.tabs.notes'))
                                    ->hiddenLabel(),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make(__('crm::filament/clusters/sales/resources/opportunity.form.sections.sales.title'))
                            ->schema([
                                Select::make('user_id')
                                    ->label(__('crm::filament/clusters/sales/resources/opportunity.form.sections.sales.fields.salesperson'))
                                    ->relationship('user', 'name', modifyQueryUsing: fn (Builder $query, Get $get): Builder => static::scopeSalespeople($query, $get('company_id')))
                                    ->searchable()
                                    ->preload()
                                    ->default(fn (): ?int => auth()->id()),
                                Select::make('team_id')
                                    ->label(__('crm::filament/clusters/sales/resources/opportunity.form.sections.sales.fields.team'))
                                    ->relationship('team', 'name', modifyQueryUsing: fn (Builder $query, Get $get): Builder => $query->where(owned_by_company($get('company_id'))))
                                    ->searchable()
                                    ->preload()
                                    ->hidden($withinTeam),
                                Select::make('company_id')
                                    ->label(__('crm::filament/clusters/sales/resources/opportunity.form.sections.sales.fields.company'))
                                    ->relationship('company', 'name')
                                    ->live()
                                    ->searchable()
                                    ->preload()
                                    ->default(fn (): ?int => current_company_id()),
                                DatePicker::make('expected_closing_date')
                                    ->label(__('crm::filament/clusters/sales/resources/opportunity.form.sections.sales.fields.expected-closing'))
                                    ->native(false),
                                Select::make('priority')
                                    ->label(__('crm::filament/clusters/sales/resources/opportunity.form.sections.sales.fields.priority'))
                                    ->options(OpportunityPriority::class)
                                    ->default(OpportunityPriority::Low),
                                Select::make('tags')
                                    ->label(__('crm::filament/clusters/sales/resources/opportunity.form.sections.sales.fields.tags'))
                                    ->relationship('tags', 'name')
                                    ->multiple()
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm(fn (Schema $schema): Schema => TagForm::configure($schema)),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function scopeSalespeople(Builder $query, mixed $companyId): Builder
    {
        return $query
            ->where('is_active', true)
            ->when(filled($companyId), fn (Builder $query): Builder => $query->where(fn (Builder $query): Builder => $query
                ->where('default_company_id', $companyId)
                ->orWhereHas('allowedCompanies', fn (Builder $query): Builder => $query->whereKey($companyId))));
    }
}
