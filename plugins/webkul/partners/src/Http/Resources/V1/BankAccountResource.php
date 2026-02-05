<?php

namespace Webkul\Partner\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Support\Http\Resources\V1\BankResource;

class BankAccountResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'account_number'      => $this->account_number,
            'account_holder_name' => $this->account_holder_name,
            'can_send_money'      => $this->can_send_money,
            'bank_id'             => $this->bank_id,
            'created_at'          => $this->created_at,
            'updated_at'          => $this->updated_at,
            'bank'                => new BankResource($this->whenLoaded('bank')),
        ];
    }
}
