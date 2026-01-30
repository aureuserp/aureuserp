<?php

namespace Webkul\Account\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Security\Http\Resources\V1\UserResource;
use Webkul\Support\Http\Resources\V1\CurrencyResource;

class PartialReconcileResource extends JsonResource
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
            'debit_move_id'            => $this->debit_move_id,
            'credit_move_id'           => $this->credit_move_id,
            'full_reconcile_id'        => $this->full_reconcile_id,
            'exchange_move_id'         => $this->exchange_move_id,
            'debit_currency_id'        => $this->debit_currency_id,
            'credit_currency_id'       => $this->credit_currency_id,
            'company_id'               => $this->company_id,
            'created_by'               => $this->created_by,
            'max_date'                 => $this->max_date,
            'amount'                   => $this->amount,
            'debit_amount_currency'    => $this->debit_amount_currency,
            'credit_amount_currency'   => $this->credit_amount_currency,
            'created_at'               => $this->created_at,
            'updated_at'               => $this->updated_at,
            'debitMove'                => new MoveLineResource($this->whenLoaded('debitMove')),
            'creditMove'               => new MoveLineResource($this->whenLoaded('creditMove')),
            'fullReconcile'            => new FullReconcileResource($this->whenLoaded('fullReconcile')),
            'exchangeMove'             => new MoveResource($this->whenLoaded('exchangeMove')),
            'debitCurrency'            => new CurrencyResource($this->whenLoaded('debitCurrency')),
            'createdBy'                => new UserResource($this->whenLoaded('createdBy')),
        ];
    }
}
