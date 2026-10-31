<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Actions;

use Filament\Actions\Action;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Validation\ValidationException;
use Webkul\Crm\Enums\LeadConversionCustomer;
use Webkul\Crm\Enums\QuotationPartnerAction;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource\Actions\ConvertToOpportunityAction;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource;
use Webkul\Crm\Models\Lead;
use Webkul\Crm\Models\QuotationPartner;

class NewQuotationAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'newQuotation';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('crm::filament/clusters/sales/resources/opportunity.actions.new-quotation.label'))
            ->icon('heroicon-o-document-plus')
            ->color('primary')
            ->visible(fn (Lead $record): bool => ! $record->isLead() && ! $record->trashed() && QuotationResource::canCreate())
            ->authorize(fn (Lead $record): bool => QuotationResource::canCreate() && OpportunityResource::canView($record))
            ->modalHeading(__('crm::filament/clusters/sales/resources/opportunity.actions.new-quotation.modal-heading'))
            ->modalDescription(__('crm::filament/clusters/sales/resources/opportunity.actions.new-quotation.modal-description'))
            ->modalSubmitActionLabel(__('crm::filament/clusters/sales/resources/opportunity.actions.new-quotation.submit'))
            ->modalHidden(fn (Lead $record): bool => filled($record->partner_id))
            ->schema(fn (Lead $record): array => filled($record->partner_id) ? [] : [
                Radio::make('customer')
                    ->label(__('crm::filament/clusters/leads/resources/lead.actions.convert.form.customer'))
                    ->options(collect(static::customerChoices($record))->mapWithKeys(fn (LeadConversionCustomer $choice): array => [$choice->value => $choice->getLabel()])->all())
                    ->enum(LeadConversionCustomer::class)
                    ->default(static::customerChoices($record)[0])
                    ->required()
                    ->live(),
                Select::make('partner_id')
                    ->label(__('crm::filament/clusters/leads/resources/lead.actions.convert.form.existing-customer'))
                    ->searchable()
                    ->getSearchResultsUsing(fn (string $search): array => ConvertToOpportunityAction::customersFor($record)
                        ->where('name', 'like', '%'.$search.'%')
                        ->orderBy('name')
                        ->limit(50)
                        ->pluck('name', 'id')
                        ->all())
                    ->getOptionLabelUsing(fn ($value): ?string => ConvertToOpportunityAction::customersFor($record)->find($value)?->name)
                    ->visible(fn (Get $get): bool => $get('customer') === LeadConversionCustomer::LINK)
                    ->required(fn (Get $get): bool => $get('customer') === LeadConversionCustomer::LINK),
            ])
            ->action(function (Lead $record, array $data, Action $action): void {
                $choice = $data['customer'] ?? LeadConversionCustomer::NONE;

                abort_unless(in_array($choice, static::customerChoices($record), true), 403);

                $hadCustomer = filled($record->partner_id);

                if (! $hadCustomer) {
                    match ($choice) {
                        LeadConversionCustomer::CREATE => $record->partner()->associate($record->createCustomer())->save(),
                        LeadConversionCustomer::LINK   => $this->linkCustomer($record, $data['partner_id'] ?? null),
                        default                        => null,
                    };
                }

                QuotationPartner::query()->create([
                    'opportunity_id' => $record->getKey(),
                    'action'         => $hadCustomer ? QuotationPartnerAction::EXISTING : QuotationPartnerAction::from($choice->value),
                    'partner_id'     => $record->partner_id,
                    'company_id'     => $record->company_id,
                ]);

                $action->redirect(QuotationResource::getUrl('create', ['opportunity' => $record->getKey()]));
            });
    }

    public static function customerChoices(Lead $record): array
    {
        return OpportunityResource::canEdit($record) ? LeadConversionCustomer::cases() : [LeadConversionCustomer::NONE];
    }

    protected function linkCustomer(Lead $record, mixed $partnerId): void
    {
        $partner = ConvertToOpportunityAction::customersFor($record)->find($partnerId);

        if (! $partner) {
            throw ValidationException::withMessages(['mountedActions.0.data.partner_id' => __('validation.exists', ['attribute' => 'customer'])]);
        }

        $record->partner()->associate($partner)->save();
    }
}
