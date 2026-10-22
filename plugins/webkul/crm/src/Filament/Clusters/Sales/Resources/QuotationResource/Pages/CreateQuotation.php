<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource\Pages;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource;
use Webkul\Crm\Models\Lead;
use Webkul\Crm\Models\QuotationPartner;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource\Pages\CreateQuotation as BaseCreateQuotation;
use Webkul\Sale\Models\Partner;

class CreateQuotation extends BaseCreateQuotation
{
    protected static string $resource = QuotationResource::class;

    #[Url(as: 'opportunity')]
    public ?int $opportunityId = null;

    protected ?Lead $cachedOpportunity = null;

    public function getOpportunity(): ?Lead
    {
        if (blank($this->opportunityId)) {
            return null;
        }

        if ($this->cachedOpportunity?->getKey() === $this->opportunityId) {
            return $this->cachedOpportunity;
        }

        $opportunity = OpportunityResource::getEloquentQuery()->whereNull('deleted_at')->find($this->opportunityId);

        return $this->cachedOpportunity = $opportunity && OpportunityResource::canView($opportunity) ? $opportunity : null;
    }

    protected function afterFill(): void
    {
        $opportunity = $this->getOpportunity();

        if (! $opportunity) {
            return;
        }

        $this->form->fill([
            ...$this->form->getRawState(),
            ...array_filter([
                'origin'        => $opportunity->name,
                'campaign_id'   => $opportunity->utm_campaign_id,
                'medium_id'     => $opportunity->utm_medium_id,
                'utm_source_id' => $opportunity->utm_source_id,
                'company_id'    => $opportunity->company_id,
            ], fn (mixed $value): bool => filled($value)),
        ]);

        if ($opportunity->partner_id && Partner::query()->whereKey($opportunity->partner_id)->exists()) {
            $this->data['partner_id'] = $opportunity->partner_id;

            $this->form->getComponentByStatePath('partner_id')?->callAfterStateUpdated();
        }

        if ($opportunity->user_id) {
            $this->data['user_id'] = $opportunity->user_id;
        }
    }

    protected function afterCreate(): void
    {
        if ($opportunity = $this->getOpportunity()) {
            $this->getRecord()->forceFill([
                'opportunity_id' => $opportunity->getKey(),
                'team_id'        => $this->getRecord()->team_id ?? $opportunity->team_id,
            ])->saveQuietly();

            QuotationPartner::query()
                ->where('opportunity_id', $opportunity->getKey())
                ->where('creator_id', Auth::id())
                ->whereNull('quotation_id')
                ->latest('id')
                ->first()
                ?->update(['quotation_id' => $this->getRecord()->getKey()]);
        }

        parent::afterCreate();
    }

    public function getSubheading(): ?string
    {
        $opportunity = $this->getOpportunity();

        return $opportunity
            ? __('crm::filament/clusters/sales/resources/opportunity.actions.new-quotation.subheading', ['name' => $opportunity->name])
            : parent::getSubheading();
    }
}
