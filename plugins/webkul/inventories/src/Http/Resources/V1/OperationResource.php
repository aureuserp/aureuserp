<?php

namespace Webkul\Inventory\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Partner\Http\Resources\V1\PartnerResource;
use Webkul\Security\Http\Resources\V1\UserResource;
use Webkul\Support\Http\Resources\V1\CompanyResource;

class OperationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                   => $this->id,
            'name'                 => $this->name,
            'origin'               => $this->origin,
            'move_type'            => $this->move_type?->value,
            'state'                => $this->state?->value,
            'is_favorite'          => (bool) $this->is_favorite,
            'description'          => $this->description,
            'has_deadline_issue'   => (bool) $this->has_deadline_issue,
            'is_printed'           => (bool) $this->is_printed,
            'is_locked'            => (bool) $this->is_locked,
            'deadline'             => $this->deadline,
            'scheduled_at'         => $this->scheduled_at,
            'closed_at'            => $this->closed_at,
            'user'                 => UserResource::make($this->whenLoaded('user')),
            'owner'                => UserResource::make($this->whenLoaded('owner')),
            'operation_type'       => OperationTypeResource::make($this->whenLoaded('operationType')),
            'source_location'      => LocationResource::make($this->whenLoaded('sourceLocation')),
            'destination_location' => LocationResource::make($this->whenLoaded('destinationLocation')),
            'back_order'           => OperationResource::make($this->whenLoaded('backOrder')),
            'return'               => OperationResource::make($this->whenLoaded('return')),
            'partner'              => PartnerResource::make($this->whenLoaded('partner')),
            'company'              => CompanyResource::make($this->whenLoaded('company')),
            'creator'              => UserResource::make($this->whenLoaded('creator')),
            'moves'                => MoveResource::collection($this->whenLoaded('moves')),
            'move_lines'           => MoveLineResource::collection($this->whenLoaded('moveLines')),
            'created_at'           => $this->created_at,
            'updated_at'           => $this->updated_at,
        ];
    }
}
