<?php

namespace Webkul\Account\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Security\Http\Resources\V1\UserResource;

class FullReconcileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'exchange_move_id'  => $this->exchange_move_id,
            'creator_id'        => $this->creator_id,
            'created_at'        => $this->created_at,
            'updated_at'        => $this->updated_at,
            'exchangeMove'      => new MoveResource($this->whenLoaded('exchangeMove')),
            'creator'           => new UserResource($this->whenLoaded('creator')),
        ];
    }
}
