<?php

namespace Webkul\Recruitment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Webkul\Chatter\Traits\HasChatter;
use Webkul\Chatter\Traits\HasLogActivity;
use Webkul\Employee\Models\Employee;
use Webkul\Partner\Models\Partner;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

class Candidate extends Model
{
    use HasChatter, HasLogActivity, SoftDeletes;

    protected $table = 'recruitments_candidates';

    public function getModelTitle(): string
    {
        return __('recruitments::models/candidate.title');
    }

    protected $fillable = [
        'message_bounced',
        'company_id',
        'partner_id',
        'degree_id',
        'manager_id',
        'employee_id',
        'creator_id',
        'email_cc',
        'name',
        'email_from',
        'priority',
        'phone',
        'linkedin_profile',
        'availability_date',
        'candidate_properties',
        'is_active',
    ];

    public function getLogAttributeLabels(): array
    {
        return [
            'company.name'      => __('recruitments::models/candidate.log-attributes.company'),
            'partner.name'      => __('recruitments::models/candidate.log-attributes.contact'),
            'degree.name'       => __('recruitments::models/candidate.log-attributes.degree'),
            'user.name'         => __('recruitments::models/candidate.log-attributes.manager'),
            'employee.name'     => __('recruitments::models/candidate.log-attributes.employee'),
            'creator.name'      => __('recruitments::models/candidate.log-attributes.creator'),
            'phone_sanitized'   => __('recruitments::models/candidate.log-attributes.phone'),
            'email_normalized'  => __('recruitments::models/candidate.log-attributes.email'),
            'email_cc'          => __('recruitments::models/candidate.log-attributes.email_cc'),
            'name'              => __('recruitments::models/candidate.log-attributes.name'),
            'email_from'        => __('recruitments::models/candidate.log-attributes.email_from'),
            'phone'             => __('recruitments::models/candidate.log-attributes.phone_raw'),
            'linkedin_profile'  => __('recruitments::models/candidate.log-attributes.linkedin_profile'),
            'availability_date' => __('recruitments::models/candidate.log-attributes.availability_date'),
            'is_active'         => __('recruitments::models/candidate.log-attributes.is_active'),
        ];
    }

    protected $casts = [
        'candidate_properties' => 'array',
        'is_active'            => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class, 'partner_id');
    }

    public function degree()
    {
        return $this->belongsTo(Degree::class, 'degree_id');
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function categories()
    {
        return $this->belongsToMany(ApplicantCategory::class, 'recruitments_candidate_applicant_categories', 'candidate_id', 'category_id');
    }

    public function skills(): HasMany
    {
        return $this->hasMany(CandidateSkill::class, 'candidate_id');
    }

    public function createEmployee()
    {
        $employee = $this->employee()->create([
            'name'          => $this->name,
            'user_id'       => $this->user_id,
            'department_id' => $this->department_id,
            'company_id'    => $this->company_id,
            'partner_id'    => $this->partner_id,
            'company_id'    => $this->company_id,
            'work_email'    => $this->email_from,
            'mobile_phone'  => $this->phone,
            'is_active'     => true,
        ]);

        $this->update([
            'employee_id' => $employee->id,
        ]);

        return $employee;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($candidate) {
            $authUser = Auth::user();

            $candidate->creator_id ??= $authUser->id;

            $candidate->company_id ??= $authUser?->default_company_id;
        });

        static::saved(function (self $candidate) {
            if (! $candidate->partner_id) {
                $candidate->handlePartnerCreation($candidate);
            } else {
                $candidate->handlePartnerUpdation($candidate);
            }
        });
    }

    private function handlePartnerCreation(self $candidate)
    {
        $partner = $candidate->partner()->create([
            'creator_id' => Auth::user()->id ?? $candidate->id,
            'sub_type'   => 'partner',
            'company_id' => $candidate->company_id,
            'phone'      => $candidate->phone,
            'email'      => $candidate->email_from,
            'name'       => $candidate->name,
        ]);

        $candidate->partner_id = $partner->id;
        $candidate->save();
    }

    private function handlePartnerUpdation(self $candidate)
    {
        $partner = Partner::updateOrCreate(
            ['id' => $candidate->partner_id],
            [
                'creator_id' => Auth::user()->id ?? $candidate->id,
                'sub_type'   => 'partner',
                'company_id' => $candidate->company_id,
                'phone'      => $candidate->phone,
                'email'      => $candidate->email_from,
                'name'       => $candidate->name,
            ]
        );

        if ($candidate->partner_id !== $partner->id) {
            $candidate->partner_id = $partner->id;
            $candidate->save();
        }
    }
}
