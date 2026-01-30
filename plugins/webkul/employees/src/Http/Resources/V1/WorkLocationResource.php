<?php

namespace Webkul\Employee\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Security\Http\Resources\V1\UserResource;
use Webkul\Support\Http\Resources\V1\CompanyResource;

class WorkLocationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'name'            => $this->name,
            'location_type'   => $this->location_type?->value,
            'location_number' => $this->location_number,
            'is_active'       => (bool) $this->is_active,
            'company_id'      => $this->company_id,
            'creator_id'      => $this->creator_id,
            'created_at'      => $this->created_at,
            'updated_at'      => $this->updated_at,
            'deleted_at'      => $this->deleted_at,
            'company'         => new CompanyResource($this->whenLoaded('company')),
            'creator'         => new UserResource($this->whenLoaded('createdBy')),
        ];
    }
}
