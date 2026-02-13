<?php

namespace Webkul\Recruitment\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Employee\Http\Resources\V1\EmployeeResource;
use Webkul\Partner\Http\Resources\V1\PartnerResource;
use Webkul\Security\Http\Resources\V1\UserResource;
use Webkul\Support\Http\Resources\V1\CompanyResource;

class CandidateResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                   => $this->id,
            'message_bounced'      => $this->message_bounced,
            'email_cc'             => $this->email_cc,
            'name'                 => $this->name,
            'email_from'           => $this->email_from,
            'priority'             => $this->priority,
            'phone'                => $this->phone,
            'linkedin_profile'     => $this->linkedin_profile,
            'availability_date'    => $this->availability_date,
            'candidate_properties' => $this->candidate_properties,
            'is_active'            => (bool) $this->is_active,
            'company_id'           => $this->company_id,
            'partner_id'           => $this->partner_id,
            'degree_id'            => $this->degree_id,
            'manager_id'           => $this->manager_id,
            'employee_id'          => $this->employee_id,
            'creator_id'           => $this->creator_id,
            'created_at'           => $this->created_at,
            'updated_at'           => $this->updated_at,
            'deleted_at'           => $this->deleted_at,
            'company'              => new CompanyResource($this->whenLoaded('company')),
            'partner'              => new PartnerResource($this->whenLoaded('partner')),
            'degree'               => new DegreeResource($this->whenLoaded('degree')),
            'manager'              => new UserResource($this->whenLoaded('manager')),
            'employee'             => new EmployeeResource($this->whenLoaded('employee')),
            'createdBy'            => new UserResource($this->whenLoaded('createdBy')),
            'categories'           => ApplicantCategoryResource::collection($this->whenLoaded('categories')),
            'applications'         => ApplicantResource::collection($this->whenLoaded('applications')),
        ];
    }
}
