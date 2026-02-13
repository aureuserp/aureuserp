<?php

namespace Webkul\Account\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Partner\Http\Resources\V1\BankAccountResource;
use Webkul\Security\Http\Resources\V1\UserResource;
use Webkul\Support\Http\Resources\V1\CompanyResource;
use Webkul\Support\Http\Resources\V1\CurrencyResource;

class JournalResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                             => $this->id,
            'name'                           => $this->name,
            'code'                           => $this->code,
            'type'                           => $this->type,
            'color'                          => $this->color,
            'sort'                           => $this->sort,
            'show_on_dashboard'              => $this->show_on_dashboard,
            'refund_order'                   => $this->refund_order,
            'payment_order'                  => $this->payment_order,
            'company_id'                     => $this->company_id,
            'currency_id'                    => $this->currency_id,
            'default_account_id'             => $this->default_account_id,
            'suspense_account_id'            => $this->suspense_account_id,
            'profit_account_id'              => $this->profit_account_id,
            'loss_account_id'                => $this->loss_account_id,
            'bank_account_id'                => $this->bank_account_id,
            'creator_id'                     => $this->creator_id,
            'created_at'                     => $this->created_at,
            'updated_at'                     => $this->updated_at,
            'company'                        => CompanyResource::make($this->whenLoaded('company')),
            'currency'                       => CurrencyResource::make($this->whenLoaded('currency')),
            'defaultAccount'                 => AccountResource::make($this->whenLoaded('defaultAccount')),
            'suspenseAccount'                => AccountResource::make($this->whenLoaded('suspenseAccount')),
            'profitAccount'                  => AccountResource::make($this->whenLoaded('profitAccount')),
            'lossAccount'                    => AccountResource::make($this->whenLoaded('lossAccount')),
            'bankAccount'                    => BankAccountResource::make($this->whenLoaded('bankAccount')),
            'creator'                        => UserResource::make($this->whenLoaded('creator')),
            'inboundPaymentMethodLines'      => PaymentMethodLineResource::collection($this->whenLoaded('inboundPaymentMethodLines')),
            'outboundPaymentMethodLines'     => PaymentMethodLineResource::collection($this->whenLoaded('outboundPaymentMethodLines')),
        ];
    }
}
