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
            'created_id'        => $this->created_id,
            'created_at'        => $this->created_at,
            'updated_at'        => $this->updated_at,
            'exchangeMove'      => new MoveResource($this->whenLoaded('exchangeMove')),
            'createdBy'         => new UserResource($this->whenLoaded('createdBy')),
        ];
    }
}
