<?php

namespace Webkul\Purchase\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Account\Http\Resources\V1\FiscalPositionResource;
use Webkul\Account\Http\Resources\V1\IncotermResource;
use Webkul\Account\Http\Resources\V1\PartnerResource as AccountPartnerResource;
use Webkul\Account\Http\Resources\V1\PaymentTermResource;
use Webkul\Inventory\Http\Resources\V1\OperationTypeResource;
use Webkul\Security\Http\Resources\V1\UserResource;
use Webkul\Support\Http\Resources\V1\CompanyResource;
use Webkul\Support\Http\Resources\V1\CurrencyResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                       => $this->id,
            'name'                     => $this->name,
            'description'              => $this->description,
            'priority'                 => $this->priority,
            'origin'                   => $this->origin,
            'partner_reference'        => $this->partner_reference,
            'state'                    => $this->state?->value,
            'invoice_status'           => $this->invoice_status?->value,
            'receipt_status'           => $this->receipt_status?->value,
            'untaxed_amount'           => (float) $this->untaxed_amount,
            'tax_amount'               => (float) $this->tax_amount,
            'total_amount'             => (float) $this->total_amount,
            'total_cc_amount'          => (float) $this->total_cc_amount,
            'currency_rate'            => (float) $this->currency_rate,
            'mail_reminder_confirmed'  => (bool) $this->mail_reminder_confirmed,
            'mail_reception_confirmed' => (bool) $this->mail_reception_confirmed,
            'mail_reception_declined'  => (bool) $this->mail_reception_declined,
            'invoice_count'            => $this->invoice_count,
            'ordered_at'               => $this->ordered_at,
            'approved_at'              => $this->approved_at,
            'planned_at'               => $this->planned_at,
            'calendar_start_at'        => $this->calendar_start_at,
            'incoterm_location'        => $this->incoterm_location,
            'effective_date'           => $this->effective_date,
            'report_grids'             => (bool) $this->report_grids,
            'requisition_id'           => $this->requisition_id,
            'purchases_group_id'       => $this->purchases_group_id,
            'partner_id'               => $this->partner_id,
            'currency_id'              => $this->currency_id,
            'fiscal_position_id'       => $this->fiscal_position_id,
            'payment_term_id'          => $this->payment_term_id,
            'incoterm_id'              => $this->incoterm_id,
            'user_id'                  => $this->user_id,
            'company_id'               => $this->company_id,
            'creator_id'               => $this->creator_id,
            'operation_type_id'        => $this->operation_type_id,
            'created_at'               => $this->created_at,
            'updated_at'               => $this->updated_at,
            'partner'                  => new AccountPartnerResource($this->whenLoaded('partner')),
            'currency'                 => new CurrencyResource($this->whenLoaded('currency')),
            'fiscal_position'          => new FiscalPositionResource($this->whenLoaded('fiscalPosition')),
            'payment_term'             => new PaymentTermResource($this->whenLoaded('paymentTerm')),
            'incoterm'                 => new IncotermResource($this->whenLoaded('incoterm')),
            'user'                     => new UserResource($this->whenLoaded('user')),
            'company'                  => new CompanyResource($this->whenLoaded('company')),
            'creator'                  => new UserResource($this->whenLoaded('creator')),
            'operation_type'           => new OperationTypeResource($this->whenLoaded('operationType')),
            'requisition'              => new RequisitionResource($this->whenLoaded('requisition')),
            'lines'                    => OrderLineResource::collection($this->whenLoaded('lines')),
        ];
    }
}
