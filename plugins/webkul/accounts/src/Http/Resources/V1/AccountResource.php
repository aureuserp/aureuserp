<?php

namespace Webkul\Account\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Security\Http\Resources\V1\UserResource;
use Webkul\Support\Http\Resources\V1\CompanyResource;
use Webkul\Support\Http\Resources\V1\CurrencyResource;

class AccountResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'account_type' => $this->account_type,
            'name'         => $this->name,
            'code'         => $this->code,
            'note'         => $this->note,
            'deprecated'   => $this->deprecated,
            'reconcile'    => $this->reconcile,
            'non_trade'    => $this->non_trade,
            'currency_id'  => $this->currency_id,
            'creator_id'   => $this->creator_id,
            'created_at'   => $this->created_at,
            'updated_at'   => $this->updated_at,
            'currency'     => new CurrencyResource($this->whenLoaded('currency')),
            'creator'      => new UserResource($this->whenLoaded('creator')),
            'taxes'        => TaxResource::collection($this->whenLoaded('taxes')),
            'tags'         => TagResource::collection($this->whenLoaded('tags')),
            'journals'     => JournalResource::collection($this->whenLoaded('journals')),
            'moveLines'    => MoveLineResource::collection($this->whenLoaded('moveLines')),
            'companies'    => CompanyResource::collection($this->whenLoaded('companies')),
        ];
    }
}
