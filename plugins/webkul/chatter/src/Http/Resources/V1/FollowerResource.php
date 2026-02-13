<?php

namespace Webkul\Chatter\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Partner\Http\Resources\V1\PartnerResource;

class FollowerResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'              => $this->id,
            'followable_id'   => $this->followable_id,
            'followable_type' => $this->followable_type,
            'partner_id'      => $this->partner_id,
            'followed_at'     => $this->followed_at,
            'created_at'      => $this->created_at,
            'updated_at'      => $this->updated_at,
            'partner'         => new PartnerResource($this->whenLoaded('partner')),
        ];
    }
}
