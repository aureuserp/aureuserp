<?php

namespace Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource\Actions;

use Filament\Actions\BulkAction;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource;
use Webkul\Crm\Models\Lead;
use Webkul\Crm\Models\LeadConversion;
use Webkul\Crm\Services\LeadConverter;
use Webkul\Sale\Models\Team;
use Webkul\Security\Models\User;

class MassConvertLeadsBulkAction extends BulkAction
{
    public static function getDefaultName(): ?string
    {
        return 'massConvertToOpportunities';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $label = fn (string $key): string => __('crm::filament/clusters/leads/resources/lead.actions.mass-convert.'.$key);

        $this
            ->label($label('label'))
            ->icon('heroicon-o-arrow-right-circle')
            ->color('primary')
            ->modalHeading($label('modal.heading'))
            ->modalSubmitActionLabel($label('modal.submit'))
            ->modalWidth('lg')
            ->deselectRecordsAfterCompletion()
            ->schema([
                Radio::make('customer')
                    ->label(__('crm::filament/clusters/leads/resources/lead.actions.convert.form.customer'))
                    ->options([
                        LeadConversion::CUSTOMER_CREATE => $label('form.customer-options.create'),
                        LeadConversion::CUSTOMER_LINK   => $label('form.customer-options.link'),
                        LeadConversion::CUSTOMER_NONE   => __('crm::filament/clusters/leads/resources/lead.actions.convert.form.customer-options.none'),
                    ])
                    ->default(LeadConversion::CUSTOMER_CREATE)
                    ->required(),
                Toggle::make('deduplicate')
                    ->label($label('form.deduplicate'))
                    ->helperText($label('form.deduplicate-helper'))
                    ->default(true),
                Select::make('salespeople')
                    ->label($label('form.salespeople'))
                    ->helperText($label('form.salespeople-helper'))
                    ->options(fn (): array => static::salespeople()->orderBy('name')->pluck('name', 'id')->all())
                    ->multiple()
                    ->searchable(),
                Toggle::make('force_assignment')
                    ->label($label('form.force-assignment'))
                    ->helperText($label('form.force-assignment-helper')),
                Select::make('team_id')
                    ->label(__('crm::filament/clusters/leads/resources/lead.actions.convert.form.team'))
                    ->options(fn (): array => static::teams()->orderBy('name')->pluck('name', 'id')->all())
                    ->searchable(),
            ])
            ->action(function (EloquentCollection $records, array $data): void {
                $leads = $records
                    ->filter(fn (Lead $lead): bool => $lead->isLead() && ! $lead->trashed() && LeadResource::canEdit($lead))
                    ->values();

                $data['salespeople'] = static::salespeople()->whereKey((array) ($data['salespeople'] ?? []))->pluck('id')->all();
                $data['team_id'] = filled($data['team_id'] ?? null) ? static::teams()->whereKey($data['team_id'])->value('id') : null;

                $conversion = app(LeadConverter::class)->massConvert(
                    $leads,
                    $data,
                    fn (Lead $lead): Collection => ConvertToOpportunityAction::mergeableDuplicates($lead),
                );

                Notification::make()
                    ->success()
                    ->title(__('crm::filament/clusters/leads/resources/lead.actions.mass-convert.notification.title'))
                    ->body(__('crm::filament/clusters/leads/resources/lead.actions.mass-convert.notification.body', [
                        'count'   => $conversion->converted_count,
                        'merged'  => $conversion->merged_count,
                        'skipped' => $records->count() - $conversion->converted_count,
                    ]))
                    ->send();
            });
    }

    protected static function salespeople(): Builder
    {
        $companyId = current_company_id();

        return User::query()
            ->where('is_active', true)
            ->when($companyId, fn (Builder $query): Builder => $query->where(fn (Builder $query): Builder => $query
                ->where('default_company_id', $companyId)
                ->orWhereHas('allowedCompanies', fn (Builder $query): Builder => $query->whereKey($companyId))));
    }

    protected static function teams(): Builder
    {
        return Team::query()->where(owned_by_company(current_company_id()));
    }
}
