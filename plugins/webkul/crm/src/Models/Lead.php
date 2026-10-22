<?php

namespace Webkul\Crm\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Webkul\Chatter\Traits\HasChatter;
use Webkul\Chatter\Traits\HasLogActivity;
use Webkul\Crm\Database\Factories\LeadFactory;
use Webkul\Crm\Enums\OpportunityPriority;
use Webkul\Partner\Models\Partner;
use Webkul\Sale\Models\Quotation;
use Webkul\Sale\Models\Team;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Country;
use Webkul\Support\Models\State;
use Webkul\Support\Models\UtmCampaign;
use Webkul\Support\Models\UTMMedium;
use Webkul\Support\Models\UTMSource;
use Webkul\Support\Traits\BelongsToCompany;

class Lead extends Model
{
    use BelongsToCompany;
    use HasChatter, HasFactory, HasLogActivity, SoftDeletes;

    public const ACTIVITY_PLAN_PLUGIN = 'crm';

    public const TYPE_LEAD = 'lead';

    public const TYPE_OPPORTUNITY = 'opportunity';

    protected $table = 'crm_leads';

    protected $fillable = [
        'type',
        'name',
        'partner_id',
        'company_name',
        'contact_name',
        'job_title',
        'email',
        'phone',
        'website',
        'street1',
        'street2',
        'city',
        'zip',
        'state_id',
        'country_id',
        'expected_revenue',
        'recurring_revenue',
        'recurring_plan_id',
        'probability',
        'priority',
        'expected_closing_date',
        'assigned_at',
        'closed_at',
        'converted_at',
        'description',
        'stage_id',
        'team_id',
        'user_id',
        'company_id',
        'lost_reason_id',
        'lost_feedback',
        'utm_campaign_id',
        'utm_medium_id',
        'utm_source_id',
        'referred_by',
        'creator_id',
    ];

    protected $casts = [
        'expected_revenue'      => 'decimal:4',
        'recurring_revenue'     => 'decimal:4',
        'probability'           => 'decimal:2',
        'priority'              => OpportunityPriority::class,
        'expected_closing_date' => 'date',
        'assigned_at'           => 'datetime',
        'closed_at'             => 'datetime',
        'converted_at'          => 'datetime',
    ];

    public function getModelTitle(): string
    {
        return $this->isLead()
            ? __('crm::models/lead.title')
            : __('crm::models/opportunity.title');
    }

    protected function getLogAttributeLabels(): array
    {
        return [
            'name'             => __('crm::models/opportunity.log-attributes.name'),
            'stage.name'       => __('crm::models/opportunity.log-attributes.stage'),
            'partner.name'     => __('crm::models/opportunity.log-attributes.customer'),
            'user.name'        => __('crm::models/opportunity.log-attributes.salesperson'),
            'team.name'        => __('crm::models/opportunity.log-attributes.team'),
            'expected_revenue' => __('crm::models/opportunity.log-attributes.expected-revenue'),
            'probability'      => __('crm::models/opportunity.log-attributes.probability'),
            'lostReason.name'  => __('crm::models/opportunity.log-attributes.lost-reason'),
        ];
    }

    public function scopeLeads(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_LEAD);
    }

    public function isLead(): bool
    {
        return $this->type === self::TYPE_LEAD;
    }

    public function scopeOpportunities(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_OPPORTUNITY);
    }

    public function scopeWon(Builder $query): Builder
    {
        return $query->whereHas('stage', fn (Builder $query): Builder => $query->where('is_won', true));
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->whereDoesntHave('stage', fn (Builder $query): Builder => $query->where('is_won', true));
    }

    public function isWon(): bool
    {
        return (bool) $this->stage?->is_won;
    }

    public function isLost(): bool
    {
        return $this->trashed();
    }

    public function markAsWon(): bool
    {
        $wonStageId = Stage::query()->where('is_won', true)->orderBy('sort')->value('id');

        if (! $wonStageId) {
            return false;
        }

        $this->stage_id = $wonStageId;

        return $this->save();
    }

    public function markAsLost(?int $lostReasonId, ?string $feedback = null): void
    {
        $this->forceFill([
            'lost_reason_id' => $lostReasonId,
            'lost_feedback'  => $feedback,
            'probability'    => 0,
            'closed_at'      => now(),
        ])->save();

        $this->delete();
    }

    public function convertToOpportunity(array $options = []): self
    {
        $partnerId = match ($options['customer'] ?? 'none') {
            'create' => $this->createCustomer()->getKey(),
            'link'   => $options['partner_id'] ?? null,
            default  => $this->partner_id,
        };

        $this->forceFill([
            'type'         => self::TYPE_OPPORTUNITY,
            'partner_id'   => $partnerId,
            'user_id'      => array_key_exists('user_id', $options) ? $options['user_id'] : $this->user_id,
            'team_id'      => array_key_exists('team_id', $options) ? $options['team_id'] : $this->team_id,
            'converted_at' => now(),
        ])->save();

        return $this;
    }

    public function createCustomer(): Partner
    {
        return Partner::query()->create([
            'account_type' => filled($this->company_name) ? 'company' : 'individual',
            'sub_type'     => 'partner',
            'name'         => $this->company_name ?: ($this->contact_name ?: $this->name),
            'email'        => $this->email,
            'phone'        => $this->phone,
            'website'      => $this->website,
            'job_title'    => filled($this->company_name) ? null : $this->job_title,
            'street1'      => $this->street1,
            'street2'      => $this->street2,
            'city'         => $this->city,
            'zip'          => $this->zip,
            'state_id'     => $this->state_id,
            'country_id'   => $this->country_id,
            'company_id'   => $this->company_id,
            'creator_id'   => Auth::id(),
        ]);
    }

    public static function normalizeEmail(?string $email): ?string
    {
        $email = strtolower(trim((string) $email));

        return $email !== '' ? $email : null;
    }

    public static function sanitizePhone(?string $phone): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $phone);

        return strlen($digits) >= 7 ? $digits : null;
    }

    public function similarRecordsQuery(): Builder
    {
        $email = $this->email_normalized ?? static::normalizeEmail($this->email);
        $phone = $this->phone_sanitized ?? static::sanitizePhone($this->phone);
        $partnerId = $this->partner_id;

        return static::query()
            ->withTrashed()
            ->whereKeyNot($this->getKey())
            ->where('company_id', $this->company_id)
            ->where(function (Builder $query) use ($email, $phone, $partnerId): void {
                $query->whereRaw('1 = 0')
                    ->when($email, fn (Builder $query): Builder => $query->orWhere('email_normalized', $email))
                    ->when($phone, fn (Builder $query): Builder => $query->orWhere('phone_sanitized', $phone))
                    ->when($partnerId, fn (Builder $query): Builder => $query->orWhere('partner_id', $partnerId));
            });
    }

    public function quotations(): HasMany
    {
        return $this->hasMany(Quotation::class, 'opportunity_id');
    }

    public function merges(): HasMany
    {
        return $this->hasMany(LeadMerge::class, 'lead_id');
    }

    public function crmActivities(): HasMany
    {
        return $this->hasMany(Activity::class, 'messageable_id');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class, 'state_id');
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(UtmCampaign::class, 'utm_campaign_id');
    }

    public function medium(): BelongsTo
    {
        return $this->belongsTo(UTMMedium::class, 'utm_medium_id');
    }

    public function source(): BelongsTo
    {
        return $this->belongsTo(UTMSource::class, 'utm_source_id');
    }

    public function recurringPlan(): BelongsTo
    {
        return $this->belongsTo(RecurringPlan::class, 'recurring_plan_id')->withTrashed();
    }

    public function monthlyRecurringRevenue(): float
    {
        $months = (int) ($this->recurringPlan?->number_of_months ?? 0);

        return $months > 0 ? round((float) $this->recurring_revenue / $months, 2) : 0.0;
    }

    public function stage(): BelongsTo
    {
        return $this->belongsTo(Stage::class, 'stage_id');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team_id')->withTrashed();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class, 'partner_id')->withTrashed();
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function lostReason(): BelongsTo
    {
        return $this->belongsTo(LostReason::class, 'lost_reason_id')->withTrashed();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'crm_tag_rel', 'lead_id', 'tag_id');
    }

    protected static function booted(): void
    {
        static::saving(function (self $lead): void {
            $lead->expected_revenue ??= 0;

            $lead->recurring_revenue ??= 0;

            $lead->probability ??= 0;

            $lead->priority ??= OpportunityPriority::Low;

            $lead->email_normalized = static::normalizeEmail($lead->email);

            $lead->phone_sanitized = static::sanitizePhone($lead->phone);
        });

        static::creating(function (self $lead): void {
            $lead->type ??= self::TYPE_OPPORTUNITY;

            $lead->stage_id ??= Stage::query()->orderBy('sort')->value('id');

            $lead->creator_id ??= Auth::id();

            $lead->user_id ??= Auth::id();

            $lead->assigned_at ??= $lead->user_id ? now() : null;
        });

        static::updating(function (self $lead): void {
            if ($lead->isDirty('user_id')) {
                $lead->assigned_at = $lead->user_id ? now() : null;
            }
        });

        static::saving(function (self $lead): void {
            if (! $lead->isDirty('stage_id')) {
                return;
            }

            if ($lead->stage()->where('is_won', true)->exists()) {
                $lead->probability = 100;

                $lead->closed_at ??= now();
            } elseif ($lead->getOriginal('stage_id') && Stage::query()->whereKey($lead->getOriginal('stage_id'))->where('is_won', true)->exists()) {
                $lead->closed_at = null;
            }
        });

        static::restoring(function (self $lead): void {
            $lead->lost_reason_id = null;

            $lead->lost_feedback = null;

            $lead->closed_at = null;
        });
    }

    protected static function newFactory(): LeadFactory
    {
        return LeadFactory::new();
    }
}
