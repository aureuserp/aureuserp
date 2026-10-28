<?php

namespace Webkul\Asset\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Webkul\Asset\Database\Factories\AssetFactory;
use Webkul\Asset\Enums\AssetCondition;
use Webkul\Asset\Enums\AssetSourceType;
use Webkul\Asset\Enums\AssetStatus;
use Webkul\Asset\Enums\MovementType;
use Webkul\Asset\Services\AssetCustodyService;
use Webkul\Asset\Settings\AssetSettings;
use Webkul\Chatter\Traits\HasChatter;
use Webkul\Chatter\Traits\HasLogActivity;
use Webkul\Employee\Models\Employee;
use Webkul\Field\Traits\HasCustomFields;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Services\CompanyContext;
use Webkul\Support\Support\CompanyConsistencyGuard;
use Webkul\Support\Traits\BelongsToCompany;
use Webkul\Support\Traits\ChecksCompanyConsistency;

class Asset extends Model
{
    use BelongsToCompany;
    use ChecksCompanyConsistency, HasChatter, HasCustomFields, HasFactory, HasLogActivity, SoftDeletes;

    protected $table = 'assets';

    public ?AssetSourceType $initialSourceType = null;

    public ?Model $initialReference = null;

    public ?array $initialAttachments = null;

    protected $fillable = [
        'company_id',
        'product_id',
        'warehouse_id',
        'asset_tag',
        'serial_no',
        'status',
        'condition',
        'current_employee_id',
        'current_site_id',
        'issued_at',
        'expected_return_at',
        'custody_note',
        'purchase_date',
        'warranty_until',
        'cost',
        'note',
        'creator_id',
        'initial_attachments',
    ];

    protected $casts = [
        'status'             => AssetStatus::class,
        'condition'          => AssetCondition::class,
        'issued_at'          => 'datetime',
        'expected_return_at' => 'date',
        'purchase_date'      => 'date',
        'warranty_until'     => 'date',
        'cost'               => 'decimal:4',
    ];

    public static function resolveDefaultWarehouseId(?int $companyId): ?int
    {
        $defaultId = app(AssetSettings::class)->default_warehouse_id;

        if (blank($defaultId)) {
            return null;
        }

        $exists = AssetWarehouse::withoutGlobalScopes()
            ->whereKey($defaultId)
            ->where('company_id', $companyId)
            ->where('is_active', true)
            ->whereNull('deleted_at')
            ->exists();

        return $exists ? (int) $defaultId : null;
    }

    protected static function booted(): void
    {
        static::creating(function (self $asset): void {
            if (blank($asset->company_id)) {
                $asset->company_id = app(CompanyContext::class)->currentId();
            }

            if (blank($asset->creator_id)) {
                $asset->creator_id = Auth::id();
            }

            if (blank($asset->company_id)) {
                throw ValidationException::withMessages([
                    'company_id' => __('assets::services/asset-custody.company-required'),
                ]);
            }

            if (blank($asset->product_id)) {
                throw ValidationException::withMessages([
                    'product_id' => __('assets::services/asset-custody.product-required'),
                ]);
            }

            $product = AssetProduct::withoutGlobalScopes()->withTrashed()->find($asset->product_id);

            if (! $product || $product->trashed() || ! $product->is_active) {
                throw ValidationException::withMessages([
                    'product_id' => __('assets::services/asset-custody.product-required'),
                ]);
            }

            if ((blank($asset->cost) || (float) $asset->cost === 0.0) && filled($product->cost)) {
                $asset->cost = $product->cost;
            }

            if (blank($asset->status)) {
                if (blank($asset->warehouse_id) && (filled($asset->current_employee_id) || filled($asset->current_site_id))) {
                    $asset->status = AssetStatus::Held;
                    $asset->issued_at ??= now();
                } else {
                    $asset->status = AssetStatus::Available;
                }
            }

            if ($asset->status === AssetStatus::Available && blank($asset->warehouse_id)) {
                $asset->warehouse_id = static::resolveDefaultWarehouseId((int) $asset->company_id);
            }

            CompanyConsistencyGuard::assert(
                $asset->company_id,
                [$asset->getAttributes()],
                $asset->companyConsistentFields(),
            );

            if ($asset->status === AssetStatus::Available) {
                if (blank($asset->warehouse_id) || ! AssetWarehouse::withoutGlobalScopes()->whereKey($asset->warehouse_id)->where('is_active', true)->whereNull('deleted_at')->exists()) {
                    throw ValidationException::withMessages([
                        'warehouse_id' => __('assets::services/asset-custody.warehouse-required'),
                    ]);
                }

                $asset->current_employee_id = null;
                $asset->current_site_id = null;
                $asset->issued_at = null;
                $asset->expected_return_at = null;
                $asset->custody_note = null;
            } elseif ($asset->status === AssetStatus::Held) {
                app(AssetCustodyService::class)->validateHeldState($asset);
                $asset->issued_at ??= now();

                if (app(AssetSettings::class)->require_expected_return_date && filled($asset->current_employee_id) && blank($asset->expected_return_at)) {
                    throw ValidationException::withMessages([
                        'expected_return_at' => __('assets::services/asset-custody.expected-return-required'),
                    ]);
                }
            } elseif (in_array($asset->status, [AssetStatus::Scrapped, AssetStatus::Lost], true)) {
                $asset->warehouse_id = null;
                $asset->current_employee_id = null;
                $asset->current_site_id = null;
                $asset->issued_at = null;
                $asset->expected_return_at = null;
                $asset->custody_note = null;
            }

            $settings = app(AssetSettings::class);

            if ($settings->require_serial_by_default && blank($asset->serial_no)) {
                throw ValidationException::withMessages([
                    'serial_no' => __('assets::services/asset-custody.serial-required'),
                ]);
            }

            if (blank($asset->condition)) {
                $asset->condition = AssetCondition::tryFrom((string) $settings->default_condition) ?? AssetCondition::New;
            }

            if (blank($asset->asset_tag)) {
                if (! $settings->auto_generate_tag) {
                    throw ValidationException::withMessages([
                        'asset_tag' => __('assets::services/asset-custody.tag-required'),
                    ]);
                }

                $asset->asset_tag = app(AssetCustodyService::class)->nextAssetTag((int) $asset->company_id);
            }
        });

        static::created(function (self $asset): void {
            if (in_array($asset->status, [AssetStatus::Available, AssetStatus::Held], true)) {
                AssetMovement::query()->create([
                    'company_id'           => $asset->company_id,
                    'asset_id'             => $asset->id,
                    'type'                 => MovementType::Registered,
                    'source_type'          => $asset->initialSourceType ?? AssetSourceType::Manual,
                    'reference_type'       => $asset->initialReference?->getMorphClass(),
                    'reference_id'         => $asset->initialReference?->getKey(),
                    'to_warehouse_id'      => $asset->warehouse_id,
                    'to_employee_id'       => $asset->current_employee_id,
                    'to_site_id'           => $asset->current_site_id,
                    'condition'            => $asset->condition,
                    'occurred_at'          => $asset->issued_at ?? $asset->created_at ?? now(),
                    'expected_return_at'   => $asset->expected_return_at,
                    'performed_by_user_id' => $asset->creator_id ?? Auth::id(),
                    'note'                 => $asset->custody_note ?? $asset->note,
                    'attachments'          => $asset->initialAttachments,
                ]);
            }
        });

        static::updating(function (self $asset): void {
            if ($asset->isDirty('company_id')) {
                $hasCustodyOrHistory = $asset->getOriginal('status') === AssetStatus::Held
                    || $asset->status === AssetStatus::Held
                    || AssetMovement::withoutGlobalScopes()
                        ->where('asset_id', $asset->id)
                        ->where('type', '!=', MovementType::Registered)
                        ->exists();

                if ($hasCustodyOrHistory) {
                    throw ValidationException::withMessages([
                        'company_id' => __('assets::models/asset.company.immutable'),
                    ]);
                }
            }

            if (filled($asset->company_id)) {
                CompanyConsistencyGuard::assert(
                    $asset->company_id,
                    [$asset->getAttributes()],
                    $asset->companyConsistentFields(),
                );
            }

            if ($asset->status === AssetStatus::Available) {
                if (blank($asset->warehouse_id) || ! AssetWarehouse::withoutGlobalScopes()->whereKey($asset->warehouse_id)->where('is_active', true)->whereNull('deleted_at')->exists()) {
                    throw ValidationException::withMessages([
                        'warehouse_id' => __('assets::services/asset-custody.warehouse-required'),
                    ]);
                }
            }

            if ($asset->isDirty('serial_no') && blank($asset->serial_no) && app(AssetSettings::class)->require_serial_by_default) {
                throw ValidationException::withMessages([
                    'serial_no' => __('assets::services/asset-custody.serial-required'),
                ]);
            }

            if ($asset->status === AssetStatus::Held) {
                app(AssetCustodyService::class)->validateHeldState($asset);

                if (app(AssetSettings::class)->require_expected_return_date && filled($asset->current_employee_id) && blank($asset->expected_return_at)) {
                    throw ValidationException::withMessages([
                        'expected_return_at' => __('assets::services/asset-custody.expected-return-required'),
                    ]);
                }
            }

            if ($asset->status !== AssetStatus::Available) {
                $asset->warehouse_id = null;
            }

            if ($asset->status !== AssetStatus::Held) {
                $asset->current_employee_id = null;
                $asset->current_site_id = null;
                $asset->issued_at = null;
                $asset->expected_return_at = null;
                $asset->custody_note = null;
            }
        });

        static::deleting(function (self $asset): void {
            if ($asset->status === AssetStatus::Held) {
                throw ValidationException::withMessages([
                    'asset' => __('assets::models/asset.deletion.held'),
                ]);
            }

            $hasHistory = AssetMovement::withoutGlobalScopes()
                ->where('asset_id', $asset->id)
                ->where('type', '!=', MovementType::Registered)
                ->exists();

            if ($asset->isForceDeleting()) {
                if ($hasHistory) {
                    throw ValidationException::withMessages([
                        'asset' => __('assets::models/asset.deletion.has-history'),
                    ]);
                }

                AssetMovement::$allowCleanupDelete = true;

                try {
                    AssetMovement::withoutGlobalScopes()
                        ->where('asset_id', $asset->id)
                        ->get()
                        ->each->delete();
                } finally {
                    AssetMovement::$allowCleanupDelete = false;
                }

                return;
            }

            if ($asset->status === AssetStatus::Available && $hasHistory) {
                throw ValidationException::withMessages([
                    'asset' => __('assets::models/asset.deletion.available-has-history'),
                ]);
            }
        });
    }

    public function getNameAttribute(): ?string
    {
        return $this->product?->name;
    }

    public function getModelAttribute(): ?string
    {
        return $this->product?->model;
    }

    public function setInitialAttachmentsAttribute(mixed $value): void
    {
        $this->initialAttachments = is_array($value) ? array_values($value) : ($value ? (array) $value : null);
    }

    public function getCategoryAttribute(): ?string
    {
        return $this->product?->category?->full_name;
    }

    public function getCurrentHolderNameAttribute(): ?string
    {
        if ($this->status !== AssetStatus::Held) {
            return null;
        }

        $employee = $this->currentEmployee?->name;
        $site = $this->currentSite?->full_name ?? $this->currentSite?->name;

        if ($employee && $site) {
            return "{$employee} — {$site}";
        }

        return $employee ?: ($site ?: null);
    }

    public function getModelTitle(): string
    {
        return __('assets::models/asset.title');
    }

    public function companyConsistentFields(): array
    {
        return [
            'product_id'          => AssetProduct::class,
            'warehouse_id'        => AssetWarehouse::class,
            'current_employee_id' => Employee::class,
            'current_site_id'     => AssetSite::class,
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(AssetProduct::class, 'product_id')->withTrashed();
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(AssetWarehouse::class, 'warehouse_id')->withTrashed();
    }

    public function currentEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'current_employee_id')->withTrashed();
    }

    public function currentSite(): BelongsTo
    {
        return $this->belongsTo(AssetSite::class, 'current_site_id')->withTrashed();
    }

    public function movements(): HasMany
    {
        return $this->hasMany(AssetMovement::class, 'asset_id')->latest('occurred_at')->latest('id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    protected static function newFactory(): AssetFactory
    {
        return AssetFactory::new();
    }
}
