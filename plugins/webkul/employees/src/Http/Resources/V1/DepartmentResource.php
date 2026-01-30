<?php

namespace Webkul\Employee\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Security\Http\Resources\V1\UserResource;
use Webkul\Support\Http\Resources\V1\CompanyResource;

class DepartmentResource extends JsonResource
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
            'complete_name'        => $this->complete_name,
            'parent_path'          => $this->parent_path,
            'color'                => $this->color,
            'manager_id'           => $this->manager_id,
            'company_id'           => $this->company_id,
            'parent_id'            => $this->parent_id,
            'master_department_id' => $this->master_department_id,
            'creator_id'           => $this->creator_id,
            'created_at'           => $this->created_at,
            'updated_at'           => $this->updated_at,
            'deleted_at'           => $this->deleted_at,
            'creator'              => new UserResource($this->whenLoaded('createdBy')),
            'parent'               => new DepartmentResource($this->whenLoaded('parent')),
            'company'              => new CompanyResource($this->whenLoaded('company')),
            'manager'              => new EmployeeResource($this->whenLoaded('manager')),
            'children'             => DepartmentResource::collection($this->whenLoaded('children')),
        ];
    }
}
