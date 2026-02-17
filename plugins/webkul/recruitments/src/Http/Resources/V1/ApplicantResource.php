<?php

namespace Webkul\Recruitment\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Employee\Http\Resources\V1\DepartmentResource;
use Webkul\Security\Http\Resources\V1\UserResource;
use Webkul\Support\Http\Resources\V1\CompanyResource;
use Webkul\Support\Http\Resources\V1\UTMMediumResource;
use Webkul\Support\Http\Resources\V1\UTMSourceResource;

class ApplicantResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                      => $this->id,
            'email_cc'                => $this->email_cc,
            'priority'                => $this->priority,
            'salary_proposed_extra'   => $this->salary_proposed_extra,
            'salary_expected_extra'   => $this->salary_expected_extra,
            'applicant_properties'    => $this->applicant_properties,
            'applicant_notes'         => $this->applicant_notes,
            'is_active'               => (bool) $this->is_active,
            'create_date'             => $this->create_date,
            'date_closed'             => $this->date_closed,
            'date_opened'             => $this->date_opened,
            'date_last_stage_updated' => $this->date_last_stage_updated,
            'refuse_date'             => $this->refuse_date,
            'probability'             => (float) $this->probability,
            'salary_proposed'         => (float) $this->salary_proposed,
            'salary_expected'         => (float) $this->salary_expected,
            'delay_close'             => (float) $this->delay_close,
            'state'                   => $this->state,
            'application_status'      => $this->application_status,
            'source_id'               => $this->source_id,
            'medium_id'               => $this->medium_id,
            'candidate_id'            => $this->candidate_id,
            'stage_id'                => $this->stage_id,
            'last_stage_id'           => $this->last_stage_id,
            'company_id'              => $this->company_id,
            'recruiter_id'            => $this->recruiter_id,
            'job_id'                  => $this->job_id,
            'department_id'           => $this->department_id,
            'refuse_reason_id'        => $this->refuse_reason_id,
            'creator_id'              => $this->creator_id,
            'created_at'              => $this->created_at,
            'updated_at'              => $this->updated_at,
            'deleted_at'              => $this->deleted_at,
            'source'                  => new UTMSourceResource($this->whenLoaded('source')),
            'medium'                  => new UTMMediumResource($this->whenLoaded('medium')),
            'candidate'               => new CandidateResource($this->whenLoaded('candidate')),
            'stage'                   => new StageResource($this->whenLoaded('stage')),
            'lastStage'               => new StageResource($this->whenLoaded('lastStage')),
            'company'                 => new CompanyResource($this->whenLoaded('company')),
            'recruiter'               => new UserResource($this->whenLoaded('recruiter')),
            'job'                     => new JobPositionResource($this->whenLoaded('job')),
            'department'              => new DepartmentResource($this->whenLoaded('department')),
            'refuseReason'            => new RefuseReasonResource($this->whenLoaded('refuseReason')),
            'createdBy'               => new UserResource($this->whenLoaded('createdBy')),
            'interviewer'             => UserResource::collection($this->whenLoaded('interviewer')),
        ];
    }
}
