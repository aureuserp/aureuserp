<?php

namespace Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource\Actions;

use Filament\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Webkul\Crm\Filament\Actions\MergeLeadsAction;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource;
use Webkul\Crm\Filament\Support\SimilarLeads;
use Webkul\Crm\Models\Lead;
use Webkul\Crm\Models\LeadConversion;
use Webkul\Crm\Services\LeadConverter;
use Webkul\Crm\Services\LeadMerger;
use Webkul\Partner\Models\Partner;
use Webkul\Sale\Models\Team;
use Webkul\Security\Models\User;

class ConvertToOpportunityAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'convertToOpportunity';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('crm::filament/clusters/leads/resources/lead.actions.convert.label'))
            ->icon('heroicon-o-arrow-right-circle')
            ->color('primary')
            ->modalHeading(__('crm::filament/clusters/leads/resources/lead.actions.convert.modal.heading'))
            ->modalSubmitActionLabel(__('crm::filament/clusters/leads/resources/lead.actions.convert.modal.submit'))
            ->modalWidth('lg')
            ->visible(fn (Lead $record): bool => $record->isLead() && ! $record->trashed() && LeadResource::canEdit($record))
            ->fillForm(fn (Lead $record): array => [
                'action'        => LeadConversion::ACTION_CONVERT,
                'duplicate_ids' => static::mergeableDuplicates($record)->modelKeys(),
                'customer'      => match (true) {
                    filled($record->partner_id)                                     => 'link',
                    filled($record->company_name) || filled($record->contact_name)  => 'create',
                    default                                                         => 'none',
                },
                'partner_id' => $record->partner_id,
                'user_id'    => $record->user_id,
                'team_id'    => $record->team_id,
            ])
            ->schema([
                Radio::make('action')
                    ->label(__('crm::filament/clusters/leads/resources/lead.actions.convert.form.action'))
                    ->options([
                        LeadConversion::ACTION_CONVERT => __('crm::filament/clusters/leads/resources/lead.actions.convert.form.action-options.convert'),
                        LeadConversion::ACTION_MERGE   => __('crm::filament/clusters/leads/resources/lead.actions.convert.form.action-options.merge'),
                    ])
                    ->visible(fn (Lead $record): bool => static::mergeableDuplicates($record)->isNotEmpty())
                    ->required()
                    ->live(),
                CheckboxList::make('duplicate_ids')
                    ->label(__('crm::filament/clusters/leads/resources/lead.actions.convert.form.duplicates'))
                    ->options(fn (Lead $record): array => static::mergeableDuplicates($record)
                        ->mapWithKeys(fn (Lead $duplicate): array => [$duplicate->getKey() => collect([
                            $duplicate->name,
                            $duplicate->isLead() ? __('crm::models/lead.title') : __('crm::models/opportunity.title'),
                            $duplicate->trashed() ? __('crm::filament/clusters/leads/resources/lead/pages/list-leads.tabs.lost') : $duplicate->stage?->name,
                            $duplicate->user?->name,
                        ])->filter()->implode(' · ')])
                        ->all())
                    ->visible(fn (Get $get): bool => $get('action') === LeadConversion::ACTION_MERGE)
                    ->required(fn (Get $get): bool => $get('action') === LeadConversion::ACTION_MERGE),
                Radio::make('customer')
                    ->label(__('crm::filament/clusters/leads/resources/lead.actions.convert.form.customer'))
                    ->options([
                        'create' => __('crm::filament/clusters/leads/resources/lead.actions.convert.form.customer-options.create'),
                        'link'   => __('crm::filament/clusters/leads/resources/lead.actions.convert.form.customer-options.link'),
                        'none'   => __('crm::filament/clusters/leads/resources/lead.actions.convert.form.customer-options.none'),
                    ])
                    ->required()
                    ->live(),
                Select::make('partner_id')
                    ->label(__('crm::filament/clusters/leads/resources/lead.actions.convert.form.existing-customer'))
                    ->searchable()
                    ->getSearchResultsUsing(fn (string $search, Lead $record): array => static::customersFor($record)
                        ->where('name', 'like', "%{$search}%")
                        ->limit(50)
                        ->pluck('name', 'id')
                        ->all())
                    ->getOptionLabelUsing(fn ($value, Lead $record): ?string => static::customersFor($record)->find($value)?->name)
                    ->visible(fn (Get $get): bool => $get('customer') === 'link')
                    ->required(fn (Get $get): bool => $get('customer') === 'link'),
                Select::make('user_id')
                    ->label(__('crm::filament/clusters/leads/resources/lead.actions.convert.form.salesperson'))
                    ->options(fn (Lead $record): array => static::salespeopleFor($record)->orderBy('name')->pluck('name', 'id')->all())
                    ->searchable(),
                Select::make('team_id')
                    ->label(__('crm::filament/clusters/leads/resources/lead.actions.convert.form.team'))
                    ->options(fn (Lead $record): array => static::teamsFor($record)->orderBy('name')->pluck('name', 'id')->all())
                    ->searchable(),
            ])
            ->action(function (Lead $record, array $data, Action $action): void {
                $isValid = (($data['customer'] ?? null) !== 'link' || static::customersFor($record)->whereKey($data['partner_id'] ?? null)->exists())
                    && (blank($data['user_id'] ?? null) || static::salespeopleFor($record)->whereKey($data['user_id'])->exists())
                    && (blank($data['team_id'] ?? null) || static::teamsFor($record)->whereKey($data['team_id'])->exists());

                if (! $isValid) {
                    Notification::make()
                        ->danger()
                        ->title(__('crm::filament/clusters/leads/resources/lead.actions.convert.notification.invalid.title'))
                        ->body(__('crm::filament/clusters/leads/resources/lead.actions.convert.notification.invalid.body'))
                        ->send();

                    $action->halt();
                }

                $duplicates = ($data['action'] ?? LeadConversion::ACTION_CONVERT) === LeadConversion::ACTION_MERGE
                    ? static::mergeableDuplicates($record)->whereIn('id', array_map('intval', (array) ($data['duplicate_ids'] ?? [])))->values()
                    : collect();

                if ($duplicates->isNotEmpty() && ! MergeLeadsAction::canMerge(app(LeadMerger::class)->pickHead(collect([$record, ...$duplicates])), collect([$record, ...$duplicates]))) {
                    Notification::make()
                        ->danger()
                        ->title(__('crm::filament/clusters/leads/resources/lead.actions.convert.notification.invalid.title'))
                        ->send();

                    $action->halt();
                }

                $record = app(LeadConverter::class)->convert($record, $data, $duplicates)->opportunity;

                Notification::make()
                    ->success()
                    ->title(__('crm::filament/clusters/leads/resources/lead.actions.convert.notification.title'))
                    ->body(__('crm::filament/clusters/leads/resources/lead.actions.convert.notification.body', ['name' => $record->name]))
                    ->send();

                $action->redirect(OpportunityResource::getUrl('view', ['record' => $record]));
            });
    }

    public static function mergeableDuplicates(Lead $record): Collection
    {
        $duplicates = SimilarLeads::visibleRecords($record);

        if ($duplicates->isEmpty()) {
            return $duplicates;
        }

        $records = collect([$record, ...$duplicates]);
        $head = app(LeadMerger::class)->pickHead($records);

        return MergeLeadsAction::canMerge($head, $records->reject(fn (Lead $lead): bool => $lead->is($head))->values())
            ? $duplicates
            : collect();
    }

    public static function customersFor(Lead $record): Builder
    {
        return Partner::query()->where(owned_by_company($record->company_id));
    }

    public static function teamsFor(Lead $record): Builder
    {
        return Team::query()->where(owned_by_company($record->company_id));
    }

    public static function salespeopleFor(Lead $record): Builder
    {
        return User::query()
            ->where('is_active', true)
            ->when($record->company_id, fn (Builder $query): Builder => $query->where(fn (Builder $query): Builder => $query
                ->where('default_company_id', $record->company_id)
                ->orWhereHas('allowedCompanies', fn (Builder $query): Builder => $query->whereKey($record->company_id))));
    }
}
