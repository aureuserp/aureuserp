<?php

namespace Webkul\Attendance\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;
use Webkul\Attendance\Database\Factories\AttendanceFactory;
use Webkul\Attendance\Writers\WriterRegistry;
use Webkul\Chatter\Traits\HasChatter;
use Webkul\Chatter\Traits\HasLogActivity;
use Webkul\Employee\Models\Employee;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Traits\BelongsToCompany;

class Attendance extends Model
{
    use BelongsToCompany;
    use HasChatter, HasFactory, HasLogActivity;

    public const ACTIVITY_PLAN_PLUGIN = 'attendance';

    /**
     * Shifts longer than this warn the user instead of failing: biometric
     * punches carry seconds, so the threshold compares whole minutes.
     */
    public const LONG_SHIFT_MINUTES = 14 * 60;

    /**
     * Writer-key contract (see README "Writer keys"): 'manual' for the
     * hand-written form, or '<writer>' / '<writer>:<id>' for any writer
     * plugin (e.g. 'biometric-attendance:3', 'remote'). The writer part is
     * always a registered WriterRegistry slug — never free prose — and the
     * ref part is always the writer's numeric record id. Writers without
     * records (a future 'remote' plugin with no devices) simply use the
     * bare slug. Keys are stable machine identifiers; the human text is
     * resolved live from the writer when possible, else the stored
     * source_label snapshot is used.
     */
    public const SOURCE_MANUAL = 'manual';

    public static function isValidSource(string $source): bool
    {
        if ($source === self::SOURCE_MANUAL) {
            return true;
        }

        if (! preg_match('/^('.WriterRegistry::SLUG_PATTERN.')(?::(\d+))?$/', $source, $matches)) {
            return false;
        }

        return WriterRegistry::isRegistered($matches[1]);
    }

    /**
     * Writer slug of a source key ('biometric-attendance' for
     * 'biometric-attendance:3'), or null when the key is malformed.
     */
    public static function sourceWriter(?string $source): ?string
    {
        if (! $source || ! preg_match('/^('.WriterRegistry::SLUG_PATTERN.')(?::\d+)?$/', $source, $matches)) {
            return null;
        }

        return $matches[1];
    }

    /**
     * Numeric reference of a source key (3 for 'biometric-attendance:3'),
     * or null for bare writer keys ('remote') and malformed keys.
     */
    public static function sourceReferenceId(?string $source): ?int
    {
        if (! $source || ! preg_match('/^'.WriterRegistry::SLUG_PATTERN.'(?::(\d+))?$/', $source, $matches) || ! isset($matches[1])) {
            return null;
        }

        return (int) $matches[1];
    }

    /**
     * Display text for a source: the writer's live name when resolvable,
     * else the stored human snapshot, else a humanized key so rows stay
     * readable even when the writer plugin is gone.
     */
    public static function sourceDisplayName(?string $source, ?string $label = null): string
    {
        $writer = self::sourceWriter($source);

        if ($writer !== null && $writer !== self::SOURCE_MANUAL) {
            $live = WriterRegistry::resolveLabel($writer, self::sourceReferenceId($source));

            if ($live) {
                return $live;
            }
        }

        if ($label) {
            return $label;
        }

        if ($source === self::SOURCE_MANUAL) {
            return __('attendance::filament/resources/attendance.form.source-manual');
        }

        return (string) str($source)->replace([':', '_'], ' ')->headline();
    }

    protected $table = 'attendance_attendances';

    protected $fillable = [
        'employee_id',
        'work_date',
        'check_in',
        'check_out',
        'source',
        'source_label',
        'company_id',
        'creator_id',
    ];

    protected $casts = [
        'work_date'  => 'date',
        'check_in'   => 'datetime',
        'check_out'  => 'datetime',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function getModelTitle(): string
    {
        return __('attendance::filament/resources/attendance.title');
    }

    public function getWorkedMinutesAttribute(): ?int
    {
        if (! $this->check_out) {
            return null;
        }

        return (int) floor($this->check_in->diffInMinutes($this->check_out));
    }

    protected static function booted(): void
    {
        static::creating(function (self $attendance): void {
            $attendance->creator_id ??= Auth::id();
        });

        static::saving(function (self $attendance): void {
            if (! self::isValidSource($attendance->source)) {
                throw new InvalidArgumentException("Unknown attendance source [{$attendance->source}].");
            }

            $attendance->company_id ??= $attendance->employee?->company_id;
        });
    }

    protected static function newFactory(): AttendanceFactory
    {
        return AttendanceFactory::new();
    }
}
