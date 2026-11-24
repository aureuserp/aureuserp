<?php

namespace Webkul\Employee\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Webkul\Chatter\Traits\HasChatter;
use Webkul\Chatter\Traits\HasLogActivity;
use Webkul\Employee\Database\Factories\EmployeeFactory;
use Webkul\Field\Traits\HasCustomFields;
use Webkul\Partner\Models\BankAccount;
use Webkul\Partner\Models\Partner;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Country;
use Webkul\Support\Models\Scopes\CompanyScope;
use Webkul\Support\Models\State;
use Webkul\Support\Traits\BelongsToCompany;

class Employee extends Model
{
    use BelongsToCompany;
    use HasChatter, HasCustomFields, HasFactory, HasLogActivity, SoftDeletes;

    public const ACTIVITY_PLAN_PLUGIN = 'employees';

    protected $table = 'employees_employees';

    protected $fillable = [
        'company_id',
        'user_id',
        'creator_id',
        'calendar_id',
        'department_id',
        'job_id',
        'attendance_manager_id',
        'partner_id',
        'work_location_id',
        'parent_id',
        'coach_id',
        'country_id',
        'state_id',
        'country_of_birth',
        'bank_account_id',
        'departure_reason_id',
        'name',
        'job_title',
        'work_phone',
        'mobile_phone',
        'color',
        'work_email',
        'children',
        'distance_home_work',
        'km_home_work',
        'distance_home_work_unit',
        'private_phone',
        'private_email',
        'private_street1',
        'private_street2',
        'private_city',
        'private_zip',
        'private_state_id',
        'private_country_id',
        'private_car_plate',
        'lang',
        'gender',
        'birthday',
        'marital',
        'spouse_complete_name',
        'spouse_birthdate',
        'place_of_birth',
        'ssnid',
        'sinid',
        'identification_id',
        'passport_id',
        'permit_no',
        'visa_no',
        'certificate',
        'study_field',
        'study_school',
        'emergency_contact',
        'emergency_phone',
        'employee_type',
        'barcode',
        'pin',
        'address_id',
        'time_zone',
        'work_permit',
        'leave_manager_id',
        'visa_expire',
        'work_permit_expiration_date',
        'departure_date',
        'departure_description',
        'additional_note',
        'notes',
        'is_active',
        'is_flexible',
        'is_fully_flexible',
        'work_permit_scheduled_activity',
    ];

    protected $casts = [
        'is_active'                      => 'boolean',
        'is_flexible'                    => 'boolean',
        'is_fully_flexible'              => 'boolean',
        'work_permit_scheduled_activity' => 'boolean',
    ];

    public function getLogAttributeLabels(): array
    {
        return [
            'name'                        => __('employees::models/employee.log-attributes.name'),
            'calendar.name'               => __('employees::models/employee.log-attributes.calendar'),
            'department.name'             => __('employees::models/employee.log-attributes.department'),
            'country.name'                => __('employees::models/employee.log-attributes.nationality'),
            'gender'                      => __('employees::models/employee.log-attributes.gender'),
            'marital'                     => __('employees::models/employee.log-attributes.marital'),
            'spouse_complete_name'        => __('employees::models/employee.log-attributes.spouse_complete_name'),
            'spouse_birthdate'            => __('employees::models/employee.log-attributes.spouse_birthdate'),
            'children'                    => __('employees::models/employee.log-attributes.children'),
            'place_of_birth'              => __('employees::models/employee.log-attributes.place_of_birth'),
            'countryOfBirth.name'         => __('employees::models/employee.log-attributes.country_of_birth'),
            'birthday'                    => __('employees::models/employee.log-attributes.birthday'),
            'ssnid'                       => __('employees::models/employee.log-attributes.ssnid'),
            'sinid'                       => __('employees::models/employee.log-attributes.sinid'),
            'identification_id'           => __('employees::models/employee.log-attributes.identification_id'),
            'passport_id'                 => __('employees::models/employee.log-attributes.passport_id'),
            'bankAccount.account_number'  => __('employees::models/employee.log-attributes.bank_account'),
            'permit_no'                   => __('employees::models/employee.log-attributes.permit_no'),
            'visa_no'                     => __('employees::models/employee.log-attributes.visa_no'),
            'visa_expire'                 => __('employees::models/employee.log-attributes.visa_expire'),
            'work_permit_expiration_date' => __('employees::models/employee.log-attributes.work_permit_expiration_date'),
            'additional_note'             => __('employees::models/employee.log-attributes.additional_note'),
            'certificate'                 => __('employees::models/employee.log-attributes.certificate'),
            'study_field'                 => __('employees::models/employee.log-attributes.study_field'),
            'study_school'                => __('employees::models/employee.log-attributes.study_school'),
            'emergency_contact'           => __('employees::models/employee.log-attributes.emergency_contact'),
            'emergency_phone'             => __('employees::models/employee.log-attributes.emergency_phone'),
            'distance_home_work'          => __('employees::models/employee.log-attributes.distance_home_work'),
            'distance_home_work_unit'     => __('employees::models/employee.log-attributes.distance_home_work_unit'),
            'job.name'                    => __('employees::models/employee.log-attributes.job'),
            'departureReason.name'        => __('employees::models/employee.log-attributes.departure_reason'),
            'departure_date'              => __('employees::models/employee.log-attributes.departure_date'),
        ];
    }

    public function getModelTitle(): string
    {
        return __('employees::models/employee.title');
    }

    public function privateState(): BelongsTo
    {
        return $this->belongsTo(State::class, 'private_state_id');
    }

    public function privateCountry(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'private_country_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function calendar(): BelongsTo
    {
        return $this->belongsTo(Calendar::class, 'calendar_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(EmployeeJobPosition::class, 'job_id');
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class, 'partner_id')
            ->withoutGlobalScope(CompanyScope::class);
    }

    public function workLocation(): BelongsTo
    {
        return $this->belongsTo(WorkLocation::class, 'work_location_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function coach(): BelongsTo
    {
        return $this->belongsTo(self::class, 'coach_id');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class, 'state_id');
    }

    public function countryOfBirth(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_of_birth');
    }

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class, 'bank_account_id');
    }

    public function departureReason(): BelongsTo
    {
        return $this->belongsTo(DepartureReason::class, 'departure_reason_id');
    }

    public function employmentType(): BelongsTo
    {
        return $this->belongsTo(EmploymentType::class, 'employee_type');
    }

    public function categories()
    {
        return $this->belongsToMany(EmployeeCategory::class, 'employees_employee_categories', 'employee_id', 'category_id');
    }

    public function skills(): HasMany
    {
        return $this->hasMany(EmployeeSkill::class, 'employee_id');
    }

    public function resumes()
    {
        return $this->hasMany(EmployeeResume::class, 'employee_id');
    }

    protected static function newFactory(): EmployeeFactory
    {
        return EmployeeFactory::new();
    }

    public function leaveManager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'leave_manager_id');
    }

    public function attendanceManager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'attendance_manager_id');
    }

    public function companyAddress()
    {
        return $this->belongsTo(Partner::class, 'address_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::saved(function (self $employee) {
            $employee->creator_id ??= Auth::id();

            if (! $employee->partner_id) {
                $employee->handlePartnerCreation($employee);
            } else {
                $employee->handlePartnerUpdation($employee);
            }
        });
    }

    private function handlePartnerCreation(self $employee): void
    {
        $partner = $employee->partner()->create([
            'account_type' => 'individual',
            'sub_type'     => 'employee',
            'creator_id'   => $employee->creator_id ?? Auth::id(),
            'name'         => $employee?->name,
            'email'        => $employee?->work_email ?? $employee?->private_email,
            'job_title'    => $employee?->job_title,
            'phone'        => $employee?->work_phone,
            'mobile'       => $employee?->mobile_phone,
            'color'        => $employee?->color,
            'company_id'   => $employee?->company_id,
            'user_id'      => $employee?->user_id,
        ]);

        $employee->partner_id = $partner->id;
        $employee->save();
    }

    private function handlePartnerUpdation(self $employee): void
    {
        $partner = Partner::updateOrCreate(
            ['id' => $employee->partner_id],
            [
                'account_type' => 'individual',
                'sub_type'     => 'employee',
                'creator_id'   => $employee->creator_id ?? Auth::id(),
                'name'         => $employee?->name,
                'email'        => $employee?->work_email ?? $employee?->private_email,
                'job_title'    => $employee?->job_title,
                'phone'        => $employee?->work_phone,
                'mobile'       => $employee?->mobile_phone,
                'color'        => $employee?->color,
                'company_id'   => $employee?->company_id,
                'user_id'      => $employee?->user_id,
            ]
        );

        if ($employee->partner_id !== $partner->id) {
            $employee->partner_id = $partner->id;
            $employee->save();
        }
    }
}
