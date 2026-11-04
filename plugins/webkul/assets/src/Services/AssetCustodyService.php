<?php

namespace Webkul\Asset\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Webkul\Asset\Enums\AssetCondition;
use Webkul\Asset\Enums\AssetSiteUsage;
use Webkul\Asset\Enums\AssetSourceType;
use Webkul\Asset\Enums\AssetStatus;
use Webkul\Asset\Enums\MovementType;
use Webkul\Asset\Models\Asset;
use Webkul\Asset\Models\AssetMovement;
use Webkul\Asset\Models\AssetProduct;
use Webkul\Asset\Models\AssetSite;
use Webkul\Asset\Models\AssetWarehouse;
use Webkul\Asset\Settings\AssetSettings;
use Webkul\Employee\Models\Employee;
use Webkul\Support\Services\SequenceService;
use Webkul\Support\Support\CompanyConsistencyGuard;

class AssetCustodyService
{
    public const SEQUENCE_CODE = 'assets.asset_tag';

    public function nextAssetTag(int $companyId): string
    {
        $settings = app(AssetSettings::class);
        $padding = max(3, min(8, (int) ($settings->tag_padding ?? 5)));

        $defaults = [
            'name'    => 'Company Asset Tag',
            'prefix'  => $settings->tag_prefix,
            'padding' => $padding,
        ];

        $sequence = SequenceService::ensure(static::SEQUENCE_CODE, $companyId, $defaults);

        $updates = [];
        if ($sequence->prefix !== $settings->tag_prefix) {
            $updates['prefix'] = $settings->tag_prefix;
        }
        if ((int) $sequence->padding !== $padding) {
            $updates['padding'] = $padding;
        }
        if ($updates !== []) {
            $sequence->update($updates);
        }

        do {
            $tag = SequenceService::next(static::SEQUENCE_CODE, $companyId, $defaults);
        } while (Asset::withTrashed()->where('company_id', $companyId)->where('asset_tag', $tag)->exists());

        return $tag;
    }

    public function validateHeldState(Asset $asset, ?bool $requireSite = null): void
    {
        $employeeId = $asset->current_employee_id;
        $siteId = $asset->current_site_id;

        if (blank($employeeId) && blank($siteId)) {
            $this->fail('employee_id', 'employee-required');
        }

        $companyId = (int) $asset->company_id;
        $settings = app(AssetSettings::class);
        $requireSite ??= (bool) $settings->require_site_on_custody;

        if (blank($employeeId) && filled($siteId) && ! $settings->enable_site_assets) {
            $this->fail('site_id', 'site-assets-disabled');
        }

        if (filled($employeeId)) {
            CompanyConsistencyGuard::assert(
                $companyId,
                [['employee_id' => (int) $employeeId]],
                ['employee_id' => Employee::class],
            );

            $employee = Employee::withoutGlobalScopes()
                ->withTrashed()
                ->whereKey($employeeId)
                ->lockForUpdate()
                ->first();

            if (! $employee) {
                $this->fail('employee_id', 'employee-required');
            }

            if ($employee->trashed() || ! $employee->is_active) {
                $this->fail('employee_id', 'employee-inactive');
            }

            if ($settings->prevent_duplicate_product_custody && filled($asset->product_id)) {
                $hasDuplicate = Asset::query()
                    ->where('company_id', $companyId)
                    ->where('current_employee_id', $employeeId)
                    ->where('product_id', $asset->product_id)
                    ->where('status', AssetStatus::Held)
                    ->when($asset->exists, fn ($q) => $q->where('id', '!=', $asset->id))
                    ->lockForUpdate()
                    ->exists();

                if ($hasDuplicate) {
                    $this->fail('employee_id', 'duplicate-product-custody');
                }
            }

            if ($requireSite && blank($siteId)) {
                $this->fail('site_id', 'site-required');
            }
        }

        if (filled($siteId)) {
            CompanyConsistencyGuard::assert(
                $companyId,
                [['site_id' => (int) $siteId]],
                ['site_id' => AssetSite::class],
            );

            $site = AssetSite::withoutGlobalScopes()->withTrashed()->find($siteId);

            if (! $site) {
                $this->fail('site_id', 'site-required');
            }

            if ($site->trashed() || ! $site->is_active) {
                $this->fail('site_id', 'site-inactive');
            }

            if (blank($employeeId) && $site->usage_type !== AssetSiteUsage::AssetLocation) {
                $this->fail('site_id', 'fixed-asset-requires-asset-location-site');
            }

            if (filled($employeeId) && $site->usage_type !== AssetSiteUsage::CustodyZone) {
                $this->fail('site_id', 'custody-requires-custody-zone-site');
            }
        }

        $asset->warehouse_id = null;
        if (blank($employeeId)) {
            $asset->expected_return_at = null;
        }
    }

    /**
     * @param  array{employee_id?: int|null, employee_ids?: array<int, int>|null, site_id?: int|null, allocation_mode?: string|null, quantity?: float|int|string|null, condition_out?: AssetCondition|string|null, condition?: AssetCondition|string|null, issued_at?: mixed, occurred_at?: mixed, expected_return_at?: mixed, note?: string|null, issue_note?: string|null, attachments?: array<int, string>|null, issue_attachments?: array<int, string>|null, issued_by_user_id?: int|null, performed_by_user_id?: int|null}  $data
     */
    public function issue(Asset $asset, array $data): AssetMovement
    {
        return DB::transaction(function () use ($asset, $data): AssetMovement {
            $asset = $this->lockAsset($asset);

            if ($asset->isConsumable()) {
                return $this->issueConsumableLocked($asset, $data);
            }

            if ($asset->status !== AssetStatus::Available) {
                $this->fail('asset', 'asset-not-available');
            }

            if (count(array_filter((array) ($data['employee_ids'] ?? []))) > 1) {
                $this->fail('employee_id', 'single-employee-only-for-assets');
            }

            $holder = $this->resolveHolder($asset, $data);

            $issuedAt = $data['issued_at'] ?? $data['occurred_at'] ?? now();
            $expectedReturnAt = blank($holder['employee_id']) ? null : ($data['expected_return_at'] ?? null);
            $this->assertExpectedReturnNotBeforeIssued($expectedReturnAt, $issuedAt);

            $settings = app(AssetSettings::class);

            if ($settings->require_expected_return_date && filled($holder['employee_id']) && blank($expectedReturnAt)) {
                $this->fail('expected_return_at', 'expected-return-required');
            }

            $conditionOut = $this->resolveCondition($data['condition_out'] ?? $data['condition'] ?? null, $asset->condition);

            $attachments = $this->normalizeAttachments($data['attachments'] ?? $data['issue_attachments'] ?? null);

            if ($settings->require_attachments_on_custody && $attachments === []) {
                $this->fail('attachments', 'attachments-required');
            }

            $note = $data['note'] ?? $data['issue_note'] ?? null;

            $movement = AssetMovement::query()->create([
                'company_id'           => $asset->company_id,
                'asset_id'             => $asset->id,
                'type'                 => MovementType::Issued,
                'from_warehouse_id'    => $asset->warehouse_id,
                'to_employee_id'       => $holder['employee_id'],
                'to_site_id'           => $holder['site_id'],
                'condition'            => $conditionOut,
                'occurred_at'          => $issuedAt,
                'expected_return_at'   => $expectedReturnAt,
                'performed_by_user_id' => $data['issued_by_user_id'] ?? $data['performed_by_user_id'] ?? Auth::id(),
                'note'                 => $note,
                'attachments'          => $attachments ?: null,
            ]);

            $asset->update([
                'status'              => AssetStatus::Held,
                'condition'           => $conditionOut,
                'warehouse_id'        => null,
                'current_employee_id' => $holder['employee_id'],
                'current_site_id'     => $holder['site_id'],
                'issued_at'           => $issuedAt,
                'expected_return_at'  => $expectedReturnAt,
                'custody_note'        => $note,
            ]);

            $this->log($asset, 'issued', $holder, $note);

            return $movement;
        });
    }

    public function validateConsumableHolder(Asset $asset): void
    {
        $dummy = new Asset([
            'company_id'          => $asset->company_id,
            'product_id'          => $asset->product_id,
            'current_employee_id' => $asset->current_employee_id,
            'current_site_id'     => $asset->current_site_id,
            'status'              => AssetStatus::Held,
        ]);

        $this->validateHeldState($dummy, (bool) app(AssetSettings::class)->require_site_on_consumable_issue);
    }

    /**
     * @param  array{employee_id?: int|null, employee_ids?: array<int, int>|null, site_id?: int|null, allocation_mode?: string|null, quantity?: float|int|string|null, issued_at?: mixed, occurred_at?: mixed, note?: string|null, issue_note?: string|null, attachments?: array<int, string>|null, issue_attachments?: array<int, string>|null, issued_by_user_id?: int|null, performed_by_user_id?: int|null}  $data
     */
    protected function issueConsumableLocked(Asset $asset, array $data): AssetMovement
    {
        if ($asset->status !== AssetStatus::Available || (float) $asset->quantity <= 0) {
            $this->fail('asset', 'asset-not-available');
        }

        $settings = app(AssetSettings::class);

        $quantityPerRecipient = filled($data['quantity'] ?? null)
            ? (float) $data['quantity']
            : (float) max(1, (int) ($settings->default_consumable_issue_quantity ?? 1));

        if ($quantityPerRecipient <= 0) {
            $this->fail('quantity', 'quantity-must-be-positive');
        }

        $recipients = $this->resolveConsumableRecipients($asset, $data);

        $available = (float) $asset->quantity;
        $totalQuantity = round($quantityPerRecipient * count($recipients), 4);

        if ($totalQuantity > $available) {
            $this->fail('quantity', 'insufficient-consumable-quantity', [
                'requested' => $totalQuantity,
                'available' => $available,
            ]);
        }

        $attachments = $this->normalizeAttachments($data['attachments'] ?? $data['issue_attachments'] ?? null);

        if ($settings->require_attachments_on_consumable_issue && $attachments === []) {
            $this->fail('attachments', 'attachments-required');
        }

        $issuedAt = $data['issued_at'] ?? $data['occurred_at'] ?? now();
        $note = $data['note'] ?? $data['issue_note'] ?? null;
        $performedBy = $data['issued_by_user_id'] ?? $data['performed_by_user_id'] ?? Auth::id();

        $movement = null;

        foreach ($recipients as $recipient) {
            $movement = AssetMovement::query()->create([
                'company_id'           => $asset->company_id,
                'asset_id'             => $asset->id,
                'type'                 => MovementType::Issued,
                'quantity'             => $quantityPerRecipient,
                'from_warehouse_id'    => $asset->warehouse_id,
                'to_employee_id'       => $recipient['employee_id'],
                'to_site_id'           => $recipient['site_id'],
                'condition'            => $asset->condition,
                'occurred_at'          => $issuedAt,
                'performed_by_user_id' => $performedBy,
                'note'                 => $note,
                'attachments'          => $attachments ?: null,
            ]);

            $this->log($asset, 'issued', $recipient, $note);
        }

        $remaining = round($available - $totalQuantity, 4);

        $asset->update([
            'quantity'     => max(0, $remaining),
            'status'       => $remaining <= 0 ? AssetStatus::Consumed : AssetStatus::Available,
            'warehouse_id' => $remaining <= 0 ? null : $asset->warehouse_id,
        ]);

        return $movement;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<int, array{employee_id: int|null, site_id: int|null}>
     */
    protected function resolveConsumableRecipients(Asset $asset, array $data): array
    {
        $settings = app(AssetSettings::class);
        $siteId = filled($data['site_id'] ?? null) ? (int) $data['site_id'] : null;

        if (($data['allocation_mode'] ?? null) === 'site_asset') {
            if (! $settings->enable_site_assets) {
                $this->fail('site_id', 'site-assets-disabled');
            }

            if (blank($siteId)) {
                $this->fail('site_id', 'site-required');
            }

            $this->assertConsumableRecipient($asset, null, $siteId);

            return [['employee_id' => null, 'site_id' => $siteId]];
        }

        $employeeIds = $data['employee_ids'] ?? null;

        if (! is_array($employeeIds)) {
            $employeeIds = filled($data['employee_id'] ?? null) ? [$data['employee_id']] : [];
        }

        $employeeIds = collect($employeeIds)
            ->map(fn ($id): int => (int) $id)
            ->filter()
            ->unique()
            ->values();

        if ($employeeIds->isEmpty() && filled($siteId)) {
            $custodianId = AssetSite::withoutGlobalScopes()
                ->whereNull('deleted_at')
                ->whereKey($siteId)
                ->value('default_custodian_employee_id');

            if (filled($custodianId)) {
                $employeeIds = collect([(int) $custodianId]);
            }
        }

        if ($employeeIds->isEmpty()) {
            $this->fail('employee_id', 'employee-required');
        }

        if (! $settings->enable_consumables_batch_issue && $employeeIds->count() > 1) {
            $this->fail('employee_id', 'batch-issue-disabled');
        }

        $recipients = [];

        foreach ($employeeIds as $employeeId) {
            $this->assertConsumableRecipient($asset, $employeeId, $siteId);

            $recipients[] = ['employee_id' => $employeeId, 'site_id' => $siteId];
        }

        return $recipients;
    }

    protected function assertConsumableRecipient(Asset $asset, ?int $employeeId, ?int $siteId): void
    {
        $dummy = new Asset([
            'company_id'          => $asset->company_id,
            'product_id'          => $asset->product_id,
            'current_employee_id' => $employeeId,
            'current_site_id'     => $siteId,
            'status'              => AssetStatus::Held,
        ]);
        $dummy->id = $asset->id;
        $dummy->exists = true;

        $this->validateHeldState($dummy, (bool) app(AssetSettings::class)->require_site_on_consumable_issue);
    }

    /**
     * @param  array{employee_id?: int|null, site_id?: int|null, allocation_mode?: string|null, condition_in?: AssetCondition|string|null, condition_out?: AssetCondition|string|null, condition?: AssetCondition|string|null, issued_at?: mixed, occurred_at?: mixed, expected_return_at?: mixed, note?: string|null, issue_note?: string|null, return_note?: string|null, attachments?: array<int, string>|null, issue_attachments?: array<int, string>|null, return_attachments?: array<int, string>|null, performed_by_user_id?: int|null, issued_by_user_id?: int|null}  $data
     */
    public function transfer(Asset $asset, array $data): AssetMovement
    {
        return DB::transaction(function () use ($asset, $data): AssetMovement {
            $asset = $this->lockAsset($asset);

            if ($asset->status !== AssetStatus::Held) {
                $this->fail('asset', 'asset-not-held');
            }

            $holder = $this->resolveHolder($asset, $data);
            $this->assertDifferentHolder($asset, $holder);

            $occurredAt = $data['issued_at'] ?? $data['occurred_at'] ?? now();
            $this->assertNotBeforeIssued($occurredAt, $asset->issued_at, 'issued_at');
            $expectedReturnAt = blank($holder['employee_id']) ? null : ($data['expected_return_at'] ?? null);
            $this->assertExpectedReturnNotBeforeIssued($expectedReturnAt, $occurredAt);

            $settings = app(AssetSettings::class);

            if ($settings->require_expected_return_date && filled($holder['employee_id']) && blank($expectedReturnAt)) {
                $this->fail('expected_return_at', 'expected-return-required');
            }

            $condition = $this->resolveCondition($data['condition_in'] ?? $data['condition_out'] ?? $data['condition'] ?? null, $asset->condition);

            $mergedAttachments = array_values(array_unique(array_filter(array_merge(
                (array) ($data['return_attachments'] ?? []),
                (array) ($data['issue_attachments'] ?? []),
                (array) ($data['attachments'] ?? []),
            ))));

            $attachments = $this->normalizeAttachments($mergedAttachments ?: null);

            if ($settings->require_attachments_on_custody && $attachments === []) {
                $this->fail('attachments', 'attachments-required');
            }

            if ($settings->require_attachments_on_damage && in_array($condition, [AssetCondition::Poor, AssetCondition::Damaged], true) && $attachments === []) {
                $this->fail('attachments', 'damage-attachments-required');
            }

            $note = $data['note'] ?? $data['issue_note'] ?? $data['return_note'] ?? null;

            $movement = AssetMovement::query()->create([
                'company_id'           => $asset->company_id,
                'asset_id'             => $asset->id,
                'type'                 => MovementType::Transferred,
                'from_employee_id'     => $asset->current_employee_id,
                'from_site_id'         => $asset->current_site_id,
                'to_employee_id'       => $holder['employee_id'],
                'to_site_id'           => $holder['site_id'],
                'condition'            => $condition,
                'occurred_at'          => $occurredAt,
                'expected_return_at'   => $expectedReturnAt,
                'performed_by_user_id' => $data['performed_by_user_id'] ?? $data['issued_by_user_id'] ?? Auth::id(),
                'note'                 => $note,
                'attachments'          => $attachments ?: null,
            ]);

            $asset->update([
                'condition'           => $condition,
                'warehouse_id'        => null,
                'current_employee_id' => $holder['employee_id'],
                'current_site_id'     => $holder['site_id'],
                'issued_at'           => $occurredAt,
                'expected_return_at'  => $expectedReturnAt,
                'custody_note'        => $note,
            ]);

            $this->log($asset, 'transferred', $holder, $note);

            return $movement;
        });
    }

    /**
     * @param  array{warehouse_id?: int|null, condition_in?: AssetCondition|string|null, condition?: AssetCondition|string|null, ended_at?: mixed, occurred_at?: mixed, return_note?: string|null, note?: string|null, attachments?: array<int, string>|null, return_attachments?: array<int, string>|null, performed_by_user_id?: int|null, ended_by_user_id?: int|null}  $data
     */
    public function return(Asset $asset, array $data): AssetMovement
    {
        return DB::transaction(function () use ($asset, $data): AssetMovement {
            $asset = $this->lockAsset($asset);

            if ($asset->status !== AssetStatus::Held) {
                $this->fail('asset', 'asset-not-held');
            }

            $endedAt = $data['ended_at'] ?? $data['occurred_at'] ?? now();
            $this->assertNotBeforeIssued($endedAt, $asset->issued_at);

            $conditionIn = $this->resolveCondition($data['condition_in'] ?? $data['condition'] ?? null, $asset->condition);

            $note = $data['return_note'] ?? $data['note'] ?? null;
            $isDamaged = in_array($conditionIn, [AssetCondition::Poor, AssetCondition::Damaged], true);

            if ($isDamaged && blank($note)) {
                $this->fail('return_note', 'return-note-required');
            }

            $attachments = $this->normalizeAttachments($data['attachments'] ?? $data['return_attachments'] ?? null);

            $settings = app(AssetSettings::class);

            if ($settings->require_attachments_on_custody && $attachments === []) {
                $this->fail('attachments', 'attachments-required');
            }

            if ($isDamaged && $settings->require_attachments_on_damage && $attachments === []) {
                $this->fail('attachments', 'damage-attachments-required');
            }

            $warehouseId = $this->resolveWarehouseId((int) $asset->company_id, $data['warehouse_id'] ?? null);

            $movement = AssetMovement::query()->create([
                'company_id'           => $asset->company_id,
                'asset_id'             => $asset->id,
                'type'                 => MovementType::Returned,
                'from_employee_id'     => $asset->current_employee_id,
                'from_site_id'         => $asset->current_site_id,
                'to_warehouse_id'      => $warehouseId,
                'condition'            => $conditionIn,
                'occurred_at'          => $endedAt,
                'performed_by_user_id' => $data['performed_by_user_id'] ?? $data['ended_by_user_id'] ?? Auth::id(),
                'note'                 => $note,
                'attachments'          => $attachments ?: null,
            ]);

            $asset->update([
                'status'              => AssetStatus::Available,
                'condition'           => $conditionIn,
                'warehouse_id'        => $warehouseId,
                'current_employee_id' => null,
                'current_site_id'     => null,
                'issued_at'           => null,
                'expected_return_at'  => null,
                'custody_note'        => null,
            ]);

            $holder = ($movement->from_employee_id || $movement->from_site_id) ? [
                'employee_id' => $movement->from_employee_id,
                'site_id'     => $movement->from_site_id,
            ] : null;

            $this->log(
                $asset,
                'returned',
                $holder,
                $note,
                $conditionIn,
            );

            return $movement;
        });
    }

    public function scrap(Asset $asset, array $data = []): AssetMovement
    {
        return $this->terminate($asset, AssetStatus::Scrapped, MovementType::Scrapped, 'scrapped', $data);
    }

    public function markLost(Asset $asset, array $data = []): AssetMovement
    {
        return $this->terminate($asset, AssetStatus::Lost, MovementType::Lost, 'lost', $data);
    }

    /**
     * @param  array{company_id?: int|null, initial_placement?: string|null, warehouse_id?: int|null, site_id?: int|null, current_site_id?: int|null, employee_id?: int|null, current_employee_id?: int|null, issued_at?: mixed, occurred_at?: mixed, expected_return_at?: mixed, product_id?: int|null, tracking_mode?: string, mode?: string, items?: array<int, array<string, mixed>>, quantity?: int|null, condition?: AssetCondition|string|null, purchase_date?: mixed, warranty_until?: mixed, cost?: mixed, note?: string|null, name?: string|null, category?: string|null, model?: string|null}  $data
     * @return Collection<int, Asset>
     */
    public function bulkRegister(array $data): Collection
    {
        return DB::transaction(function () use ($data): Collection {
            $settings = app(AssetSettings::class);

            $companyId = (int) ($data['company_id'] ?? current_company_id() ?? 0);

            if ($companyId === 0) {
                $this->fail('company_id', 'company-required');
            }

            $productId = $data['product_id'] ?? null;

            if (blank($productId)) {
                $this->fail('product_id', 'product-required');
            }

            CompanyConsistencyGuard::assert(
                $companyId,
                [['product_id' => (int) $productId]],
                ['product_id' => AssetProduct::class],
            );

            $product = AssetProduct::withoutGlobalScopes()
                ->whereKey($productId)
                ->where('company_id', $companyId)
                ->where('is_active', true)
                ->whereNull('deleted_at')
                ->first();

            if (! $product) {
                $this->fail('product_id', 'product-required');
            }

            $mode = $this->bulkMode($data);
            $rows = $this->bulkRows($data, $mode, $settings);

            $this->assertBulkUniqueness($companyId, $rows);

            $condition = $this->resolveCondition(
                $data['condition'] ?? null,
                AssetCondition::tryFrom((string) $settings->default_condition) ?? AssetCondition::New,
            );

            $placement = $data['initial_placement'] ?? 'warehouse';

            $warehouseId = null;
            $currentEmployeeId = null;
            $currentSiteId = null;
            $issuedAt = null;
            $expectedReturnAt = null;
            $status = AssetStatus::Available;

            if ($placement === 'site_asset') {
                if (! $settings->enable_site_assets) {
                    $this->fail('site_id', 'site-assets-disabled');
                }
                $currentSiteId = $data['current_site_id'] ?? $data['site_id'] ?? null;
                $currentEmployeeId = null;
                $status = AssetStatus::Held;
                $issuedAt = $data['issued_at'] ?? $data['occurred_at'] ?? now();
                $expectedReturnAt = null;

                $dummy = new Asset([
                    'company_id'          => $companyId,
                    'product_id'          => (int) $productId,
                    'status'              => AssetStatus::Held,
                    'current_employee_id' => null,
                    'current_site_id'     => $currentSiteId,
                ]);
                $this->validateHeldState($dummy);
            } elseif ($placement === 'employee_custody') {
                $currentEmployeeId = $data['current_employee_id'] ?? $data['employee_id'] ?? null;
                $currentSiteId = $data['current_site_id'] ?? $data['site_id'] ?? null;
                $status = AssetStatus::Held;
                $issuedAt = $data['issued_at'] ?? $data['occurred_at'] ?? now();
                $expectedReturnAt = $data['expected_return_at'] ?? null;
                $this->assertExpectedReturnNotBeforeIssued($expectedReturnAt, $issuedAt);

                if ($settings->require_expected_return_date && blank($expectedReturnAt)) {
                    $this->fail('expected_return_at', 'expected-return-required');
                }

                if ($settings->prevent_duplicate_product_custody && filled($currentEmployeeId) && count($rows) > 1) {
                    $this->fail('employee_id', 'duplicate-product-custody');
                }

                $dummy = new Asset([
                    'company_id'          => $companyId,
                    'product_id'          => (int) $productId,
                    'status'              => AssetStatus::Held,
                    'current_employee_id' => $currentEmployeeId,
                    'current_site_id'     => $currentSiteId,
                ]);
                $this->validateHeldState($dummy);
            } else {
                $warehouseId = $this->resolveWarehouseId($companyId, $data['warehouse_id'] ?? null);
                $status = AssetStatus::Available;
            }

            $initialAttachments = $this->normalizeAttachments($data['attachments'] ?? null);
            if ($status === AssetStatus::Held && $settings->require_attachments_on_custody && $initialAttachments === []) {
                $this->fail('attachments', 'attachments-required');
            }

            $shared = [
                'company_id'          => $companyId,
                'product_id'          => (int) $productId,
                'status'              => $status,
                'warehouse_id'        => $warehouseId,
                'current_employee_id' => $currentEmployeeId,
                'current_site_id'     => $currentSiteId,
                'issued_at'           => $issuedAt,
                'expected_return_at'  => $expectedReturnAt,
                'condition'           => $condition,
                'purchase_date'       => $data['purchase_date'] ?? null,
                'warranty_until'      => $data['warranty_until'] ?? null,
                'cost'                => filled($data['cost'] ?? null) && (float) $data['cost'] > 0 ? $data['cost'] : $product->cost,
                'note'                => $data['note'] ?? null,
                'creator_id'          => Auth::id(),
            ];

            $assets = new Collection;

            foreach ($rows as $row) {
                $rowCondition = filled($row['condition'] ?? null)
                    ? $this->resolveCondition($row['condition'], $condition)
                    : $condition;

                $asset = new Asset([
                    ...$shared,
                    'asset_tag' => $row['asset_tag'],
                    'serial_no' => $row['serial_no'],
                    'condition' => $rowCondition,
                    'note'      => filled($row['note'] ?? null) ? $row['note'] : $shared['note'],
                ]);

                $asset->initialSourceType = AssetSourceType::Bulk;
                $asset->initialAttachments = $initialAttachments ?: null;
                $asset->save();

                $assets->push($asset);
            }

            return $assets;
        });
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{employee_id: int|null, site_id: int|null}
     */
    protected function resolveHolder(Asset $asset, array $data): array
    {
        $allocationMode = $data['allocation_mode'] ?? null;
        $siteId = $data['site_id'] ?? null;
        $employeeId = $data['employee_id'] ?? null;

        if ($allocationMode === 'site_asset') {
            if (! app(AssetSettings::class)->enable_site_assets) {
                $this->fail('site_id', 'site-assets-disabled');
            }
            $employeeId = null;
            if (blank($siteId)) {
                $this->fail('site_id', 'site-required');
            }
        } else {
            if (blank($employeeId) && filled($siteId)) {
                $site = AssetSite::withoutGlobalScopes()->whereNull('deleted_at')->find($siteId);

                if ($site && $site->default_custodian_employee_id) {
                    $employeeId = $site->default_custodian_employee_id;
                } elseif ($site && $site->usage_type === AssetSiteUsage::AssetLocation && ! array_key_exists('allocation_mode', $data)) {
                    $employeeId = null;
                }
            }

            if (blank($employeeId) && blank($siteId)) {
                $this->fail('employee_id', 'employee-required');
            }
        }

        $dummy = new Asset([
            'company_id'          => $asset->company_id,
            'product_id'          => $asset->product_id,
            'current_employee_id' => filled($employeeId) ? (int) $employeeId : null,
            'current_site_id'     => filled($siteId) ? (int) $siteId : null,
            'status'              => AssetStatus::Held,
        ]);
        if ($asset->exists) {
            $dummy->id = $asset->id;
            $dummy->exists = true;
        }

        $this->validateHeldState($dummy);

        return [
            'employee_id' => filled($employeeId) ? (int) $employeeId : null,
            'site_id'     => filled($siteId) ? (int) $siteId : null,
        ];
    }

    public function resolveWarehouseId(int $companyId, mixed $warehouseId): int
    {
        if (blank($warehouseId)) {
            $warehouseId = Asset::resolveDefaultWarehouseId($companyId);
        }

        if (blank($warehouseId)) {
            $this->fail('warehouse_id', 'warehouse-required');
        }

        CompanyConsistencyGuard::assert(
            $companyId,
            [['warehouse_id' => (int) $warehouseId]],
            ['warehouse_id' => AssetWarehouse::class],
        );

        $warehouse = AssetWarehouse::withoutGlobalScopes()
            ->whereKey($warehouseId)
            ->where('company_id', $companyId)
            ->where('is_active', true)
            ->whereNull('deleted_at')
            ->first();

        if (! $warehouse) {
            $this->fail('warehouse_id', 'warehouse-required');
        }

        return (int) $warehouse->id;
    }

    /**
     * @param  array{employee_id: int|null, site_id: int|null}  $holder
     */
    protected function assertDifferentHolder(Asset $asset, array $holder): void
    {
        $sameEmployee = (int) ($asset->current_employee_id ?? 0) === (int) ($holder['employee_id'] ?? 0);
        $sameSite = (int) ($asset->current_site_id ?? 0) === (int) ($holder['site_id'] ?? 0);

        if ($sameEmployee && $sameSite) {
            $this->fail(blank($holder['employee_id']) ? 'site_id' : 'employee_id', 'same-holder');
        }
    }

    protected function resolveCondition(mixed $condition, AssetCondition|string|null $default = null): AssetCondition
    {
        if ($condition instanceof AssetCondition) {
            return $condition;
        }

        if (filled($condition)) {
            return AssetCondition::tryFrom((string) $condition)
                ?? $this->fail('condition', 'condition-invalid');
        }

        if ($default instanceof AssetCondition) {
            return $default;
        }

        if (filled($default)) {
            return AssetCondition::tryFrom((string) $default) ?? AssetCondition::New;
        }

        return AssetCondition::New;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function terminate(
        Asset $asset,
        AssetStatus $status,
        MovementType $movementType,
        string $chatterKey,
        array $data,
    ): AssetMovement {
        return DB::transaction(function () use ($asset, $status, $movementType, $chatterKey, $data): AssetMovement {
            $asset = $this->lockAsset($asset);

            if (! in_array($asset->status, [AssetStatus::Available, AssetStatus::Held], true)) {
                $this->fail('status', 'terminal');
            }

            $endedAt = $data['ended_at'] ?? $data['occurred_at'] ?? now();

            if ($asset->status === AssetStatus::Held) {
                $this->assertNotBeforeIssued($endedAt, $asset->issued_at);
            }

            $attachments = $this->normalizeAttachments($data['attachments'] ?? $data['return_attachments'] ?? null);

            $settings = app(AssetSettings::class);

            if ($settings->require_attachments_on_termination && $attachments === []) {
                $this->fail('attachments', 'termination-attachments-required');
            }

            if ($movementType === MovementType::Scrapped && $settings->require_attachments_on_damage && $attachments === []) {
                $this->fail('attachments', 'damage-attachments-required');
            }

            if ($settings->restrict_termination_to_managers && Auth::check() && ! Auth::user()->can('terminate', $asset)) {
                $this->fail('status', 'termination-manager-required');
            }

            $condition = $movementType === MovementType::Scrapped ? AssetCondition::Damaged : $asset->condition;
            $note = $data['note'] ?? $data['return_note'] ?? null;

            $movement = AssetMovement::query()->create([
                'company_id'           => $asset->company_id,
                'asset_id'             => $asset->id,
                'type'                 => $movementType,
                'quantity'             => $asset->quantity ?? 1,
                'from_warehouse_id'    => $asset->warehouse_id,
                'from_employee_id'     => $asset->current_employee_id,
                'from_site_id'         => $asset->current_site_id,
                'condition'            => $condition,
                'occurred_at'          => $endedAt,
                'performed_by_user_id' => $data['performed_by_user_id'] ?? $data['ended_by_user_id'] ?? Auth::id(),
                'note'                 => $note,
                'attachments'          => $attachments ?: null,
            ]);

            $asset->update([
                'status'              => $status,
                'quantity'            => $asset->isConsumable() ? 0 : 1,
                'condition'           => $condition,
                'warehouse_id'        => null,
                'current_employee_id' => null,
                'current_site_id'     => null,
                'issued_at'           => null,
                'expected_return_at'  => null,
                'custody_note'        => null,
            ]);

            $holder = ($movement->from_employee_id || $movement->from_site_id) ? [
                'employee_id' => $movement->from_employee_id,
                'site_id'     => $movement->from_site_id,
            ] : null;

            $this->log($asset, $chatterKey, $holder, $note);

            return $movement;
        });
    }

    protected function lockAsset(Asset $asset): Asset
    {
        return Asset::withoutGlobalScopes()
            ->whereKey($asset->getKey())
            ->whereNull('deleted_at')
            ->lockForUpdate()
            ->firstOrFail();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function bulkMode(array $data): string
    {
        $mode = $data['tracking_mode'] ?? $data['mode'] ?? null;

        if (in_array($mode, ['with_serials', 'without_serials'], true)) {
            return $mode;
        }

        if (! empty($data['items']) && is_array($data['items'])) {
            return 'with_serials';
        }

        return 'without_serials';
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<int, array{asset_tag: string|null, serial_no: string|null, condition?: mixed, note?: string|null}>
     */
    protected function bulkRows(array $data, string $mode, AssetSettings $settings): array
    {
        $rows = [];

        if (! empty($data['items']) && is_array($data['items'])) {
            foreach ($data['items'] as $item) {
                if (! is_array($item)) {
                    continue;
                }

                $rows[] = [
                    'asset_tag' => $this->cleanValue($item['asset_tag'] ?? $item['tag'] ?? null),
                    'serial_no' => $this->cleanValue($item['serial_no'] ?? $item['serial'] ?? null),
                    'condition' => $item['condition'] ?? null,
                    'note'      => $this->cleanValue($item['note'] ?? null),
                ];
            }
        } else {
            $quantity = (int) ($data['quantity'] ?? 0);

            for ($index = 0; $index < $quantity; $index++) {
                $rows[] = ['asset_tag' => null, 'serial_no' => null];
            }
        }

        if ($rows === []) {
            $this->fail('items', 'bulk-empty');
        }

        if (count($rows) > 200) {
            $this->fail('items', 'bulk-too-many');
        }

        if ($settings->require_serial_by_default && $mode === 'without_serials') {
            $this->fail('tracking_mode', 'serial-required');
        }

        if ($mode === 'with_serials' || $settings->require_serial_by_default) {
            $missingSerials = [];

            foreach ($rows as $row) {
                if (blank($row['serial_no'])) {
                    $missingSerials[] = $row['asset_tag'] ?? '?';
                }
            }

            if ($missingSerials !== []) {
                $this->fail('items', 'bulk-serial-required', ['values' => implode(', ', $missingSerials)]);
            }
        }

        if (! $settings->auto_generate_tag) {
            $missingTags = [];

            foreach ($rows as $row) {
                if (blank($row['asset_tag'])) {
                    $missingTags[] = $row['serial_no'] ?? '?';
                }
            }

            if ($missingTags !== []) {
                $this->fail('items', 'bulk-tag-required', ['values' => implode(', ', $missingTags)]);
            }
        }

        return $rows;
    }

    /**
     * @param  array<int, array{asset_tag: string|null, serial_no: string|null}>  $rows
     */
    protected function assertBulkUniqueness(int $companyId, array $rows): void
    {
        $serials = collect($rows)->pluck('serial_no')->filter(fn ($value) => filled($value))->values();
        $tags = collect($rows)->pluck('asset_tag')->filter(fn ($value) => filled($value))->values();

        $duplicates = $this->duplicates($serials)
            ->merge($this->duplicates($tags))
            ->unique()
            ->values();

        if ($duplicates->isNotEmpty()) {
            $this->fail('items', 'bulk-duplicate-in-batch', ['values' => $duplicates->implode(', ')]);
        }

        $existingSerials = Asset::withTrashed()
            ->where('company_id', $companyId)
            ->whereIn('serial_no', $serials->unique()->all())
            ->pluck('serial_no');

        $existingTags = Asset::withTrashed()
            ->where('company_id', $companyId)
            ->whereIn('asset_tag', $tags->unique()->all())
            ->pluck('asset_tag');

        $existing = $existingSerials->merge($existingTags)->unique()->values();

        if ($existing->isNotEmpty()) {
            $this->fail('items', 'bulk-duplicate-in-database', ['values' => $existing->implode(', ')]);
        }
    }

    /**
     * @param  SupportCollection<int, string>  $values
     * @return SupportCollection<int, string>
     */
    protected function duplicates(SupportCollection $values): SupportCollection
    {
        return $values
            ->groupBy(fn (string $value): string => mb_strtolower($value))
            ->filter(fn ($group) => $group->count() > 1)
            ->flatMap(fn ($group) => $group->unique())
            ->values();
    }

    protected function cleanValue(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    /**
     * @return array<int, string>
     */
    public function normalizeAttachments(mixed $attachments): array
    {
        if (blank($attachments)) {
            return [];
        }

        if (is_string($attachments)) {
            $decoded = json_decode($attachments, true);
            $attachments = is_array($decoded) ? $decoded : [$attachments];
        }

        return array_values(array_filter((array) $attachments, fn ($item) => filled($item)));
    }

    /**
     * @param  array{employee_id?: int|null, site_id?: int|null}|null  $holder
     */
    protected function log(
        Asset $asset,
        string $event,
        ?array $holder = null,
        ?string $note = null,
        ?AssetCondition $condition = null,
    ): void {
        $message = __("assets::services/asset-custody.chatter.{$event}", [
            'holder'    => $holder ? $this->holderLabel($holder) : '',
            'condition' => $condition?->getLabel() ?? '',
        ]);

        if (filled($note)) {
            $message .= '<br>'.nl2br(e($note));
        }

        $asset->addMessage([
            'body' => $message,
            'type' => 'comment',
        ]);
    }

    /**
     * @param  array{employee_id?: int|null, site_id?: int|null}  $holder
     */
    protected function holderLabel(array $holder): string
    {
        $employee = filled($holder['employee_id'] ?? null)
            ? Employee::query()->whereKey($holder['employee_id'])->value('name')
            : null;

        $site = filled($holder['site_id'] ?? null)
            ? (AssetSite::query()->whereKey($holder['site_id'])->value('full_name')
                ?? AssetSite::query()->whereKey($holder['site_id'])->value('name'))
            : null;

        if ($employee && $site) {
            return "{$employee} — {$site}";
        }

        return $employee ?: ($site ?: '');
    }

    protected function assertNotBeforeIssued(mixed $endedAt, mixed $issuedAt, string $field = 'ended_at'): void
    {
        if (blank($endedAt) || blank($issuedAt)) {
            return;
        }

        if (Carbon::parse($endedAt)->lt(Carbon::parse($issuedAt))) {
            $this->fail($field, 'ended-before-issued');
        }
    }

    protected function assertExpectedReturnNotBeforeIssued(mixed $expectedReturnAt, mixed $issuedAt): void
    {
        if (blank($expectedReturnAt) || blank($issuedAt)) {
            return;
        }

        if (Carbon::parse($expectedReturnAt)->startOfDay()->lt(Carbon::parse($issuedAt)->startOfDay())) {
            $this->fail('expected_return_at', 'expected-return-before-issued');
        }
    }

    protected function fail(string $field, string $key, array $replace = []): never
    {
        $message = __('assets::services/asset-custody.'.$key, $replace);

        $messages = [
            $field => $message,
        ];

        if (str_contains($field, 'attachments')) {
            $messages['attachments'] = $message;
            $messages['return_attachments'] = $message;
            $messages['issue_attachments'] = $message;
        }

        throw ValidationException::withMessages($messages);
    }
}
