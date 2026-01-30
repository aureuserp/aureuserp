<?php

namespace Webkul\Employee\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Security\Http\Resources\V1\UserResource;

class DepartureReasonResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'sort'        => $this->sort,
            'reason_code' => $this->reason_code,
            'name'        => $this->name,
            'creator_id'  => $this->creator_id,
            'created_at'  => $this->created_at,
            'updated_at'  => $this->updated_at,
            'creator'     => new UserResource($this->whenLoaded('createdBy')),
            'employees'   => EmployeeResource::collection($this->whenLoaded('employees')),
        ];
    }
}
