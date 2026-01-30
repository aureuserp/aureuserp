<?php

namespace Webkul\Recruitment\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Employee\Http\Resources\V1\EmployeeResource;
use Webkul\Partner\Http\Resources\V1\IndustryResource;
use Webkul\Partner\Http\Resources\V1\PartnerResource;
use Webkul\Security\Http\Resources\V1\UserResource;

class JobPositionResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                   => $this->id,
            'name'                 => $this->name,
            'description'          => $this->description,
            'expected_employees'   => $this->expected_employees,
            'no_of_hired_employee' => $this->no_of_hired_employee,
            'no_of_employee'       => $this->no_of_employee,
            'date_from'            => $this->date_from,
            'date_to'              => $this->date_to,
            'address_id'           => $this->address_id,
            'manager_id'           => $this->manager_id,
            'industry_id'          => $this->industry_id,
            'recruiter_id'         => $this->recruiter_id,
            'creator_id'           => $this->creator_id,
            'created_at'           => $this->created_at,
            'updated_at'           => $this->updated_at,
            'deleted_at'           => $this->deleted_at,
            'address'              => new PartnerResource($this->whenLoaded('address')),
            'manager'              => new EmployeeResource($this->whenLoaded('manager')),
            'industry'             => new IndustryResource($this->whenLoaded('industry')),
            'recruiter'            => new UserResource($this->whenLoaded('recruiter')),
            'creator'              => new UserResource($this->whenLoaded('creator')),
            'interviewers'         => UserResource::collection($this->whenLoaded('interviewers')),
        ];
    }
}
