<?php

namespace Webkul\Employee\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Partner\Http\Resources\V1\BankAccountResource;
use Webkul\Partner\Http\Resources\V1\PartnerResource;
use Webkul\Security\Http\Resources\V1\UserResource;
use Webkul\Support\Http\Resources\V1\CompanyResource;
use Webkul\Support\Http\Resources\V1\CountryResource;
use Webkul\Support\Http\Resources\V1\StateResource;

class EmployeeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                             => $this->id,
            'name'                           => $this->name,
            'job_title'                      => $this->job_title,
            'work_phone'                     => $this->work_phone,
            'mobile_phone'                   => $this->mobile_phone,
            'color'                          => $this->color,
            'work_email'                     => $this->work_email,
            'children'                       => $this->children,
            'distance_home_work'             => $this->distance_home_work,
            'km_home_work'                   => (float) $this->km_home_work,
            'distance_home_work_unit'        => $this->distance_home_work_unit,
            'private_phone'                  => $this->private_phone,
            'private_email'                  => $this->private_email,
            'private_street1'                => $this->private_street1,
            'private_street2'                => $this->private_street2,
            'private_city'                   => $this->private_city,
            'private_zip'                    => $this->private_zip,
            'private_car_plate'              => $this->private_car_plate,
            'lang'                           => $this->lang,
            'gender'                         => $this->gender,
            'birthday'                       => $this->birthday,
            'marital'                        => $this->marital,
            'spouse_complete_name'           => $this->spouse_complete_name,
            'spouse_birthdate'               => $this->spouse_birthdate,
            'place_of_birth'                 => $this->place_of_birth,
            'ssnid'                          => $this->ssnid,
            'sinid'                          => $this->sinid,
            'identification_id'              => $this->identification_id,
            'passport_id'                    => $this->passport_id,
            'permit_no'                      => $this->permit_no,
            'visa_no'                        => $this->visa_no,
            'certificate'                    => $this->certificate,
            'study_field'                    => $this->study_field,
            'study_school'                   => $this->study_school,
            'emergency_contact'              => $this->emergency_contact,
            'emergency_phone'                => $this->emergency_phone,
            'employee_type'                  => $this->employee_type,
            'barcode'                        => $this->barcode,
            'pin'                            => $this->pin,
            'time_zone'                      => $this->time_zone,
            'work_permit'                    => $this->work_permit,
            'visa_expire'                    => $this->visa_expire,
            'work_permit_expiration_date'    => $this->work_permit_expiration_date,
            'departure_date'                 => $this->departure_date,
            'departure_description'          => $this->departure_description,
            'additional_note'                => $this->additional_note,
            'notes'                          => $this->notes,
            'is_active'                      => (bool) $this->is_active,
            'is_flexible'                    => (bool) $this->is_flexible,
            'is_fully_flexible'              => (bool) $this->is_fully_flexible,
            'work_permit_scheduled_activity' => (bool) $this->work_permit_scheduled_activity,
            'company_id'                     => $this->company_id,
            'user_id'                        => $this->user_id,
            'creator_id'                     => $this->creator_id,
            'calendar_id'                    => $this->calendar_id,
            'department_id'                  => $this->department_id,
            'job_id'                         => $this->job_id,
            'attendance_manager_id'          => $this->attendance_manager_id,
            'partner_id'                     => $this->partner_id,
            'work_location_id'               => $this->work_location_id,
            'parent_id'                      => $this->parent_id,
            'coach_id'                       => $this->coach_id,
            'country_id'                     => $this->country_id,
            'state_id'                       => $this->state_id,
            'country_of_birth'               => $this->country_of_birth,
            'bank_account_id'                => $this->bank_account_id,
            'departure_reason_id'            => $this->departure_reason_id,
            'private_state_id'               => $this->private_state_id,
            'private_country_id'             => $this->private_country_id,
            'address_id'                     => $this->address_id,
            'leave_manager_id'               => $this->leave_manager_id,
            'created_at'                     => $this->created_at,
            'updated_at'                     => $this->updated_at,
            'deleted_at'                     => $this->deleted_at,
            'company'                        => new CompanyResource($this->whenLoaded('company')),
            'user'                           => new UserResource($this->whenLoaded('user')),
            'creator'                        => new UserResource($this->whenLoaded('creator')),
            'calendar'                       => new CalendarResource($this->whenLoaded('calendar')),
            'department'                     => new DepartmentResource($this->whenLoaded('department')),
            'partner'                        => new PartnerResource($this->whenLoaded('partner')),
            'work_location'                  => new WorkLocationResource($this->whenLoaded('workLocation')),
            'parent'                         => new EmployeeResource($this->whenLoaded('parent')),
            'coach'                          => new EmployeeResource($this->whenLoaded('coach')),
            'country'                        => new CountryResource($this->whenLoaded('country')),
            'state'                          => new StateResource($this->whenLoaded('state')),
            'bank_account'                   => new BankAccountResource($this->whenLoaded('bankAccount')),
            'private_state'                  => new StateResource($this->whenLoaded('privateState')),
            'private_country'                => new CountryResource($this->whenLoaded('privateCountry')),
            'country_of_birth'               => new CountryResource($this->whenLoaded('countryOfBirth')),
            'attendance_manager'             => new UserResource($this->whenLoaded('attendanceManager')),
            'leave_manager'                  => new UserResource($this->whenLoaded('leaveManager')),
            'departure_reason'               => new DepartureReasonResource($this->whenLoaded('departureReason')),
        ];
    }
}
