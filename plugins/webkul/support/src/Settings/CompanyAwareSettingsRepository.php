<?php

namespace Webkul\Support\Settings;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\LaravelSettings\SettingsRepositories\DatabaseSettingsRepository;

class CompanyAwareSettingsRepository extends DatabaseSettingsRepository
{
    public const COMPANY_SCOPED_GROUPS = [
        'accounts_accounts',
        'accounts_taxes',
    ];

    protected static ?bool $hasCompanyColumn = null;

    public function getPropertiesInGroup(string $group): array
    {
        if (! $this->isCompanyScoped($group)) {
            return parent::getPropertiesInGroup($group);
        }

        $defaultId = $this->defaultCompanyId();
        $currentId = $this->currentCompanyId();

        $load = fn ($builder) => $builder
            ->get(['name', 'payload'])
            ->mapWithKeys(fn (object $row) => [$row->name => $this->decode($row->payload, true)])
            ->toArray();

        $base = $load(
            $this->getBuilder()
                ->where('group', $group)
                ->where(fn ($query) => $query->where('company_id', $defaultId)->orWhereNull('company_id'))
                ->orderByRaw('company_id IS NULL DESC')
        );

        if ($currentId === $defaultId) {
            return $base;
        }

        $override = $load(
            $this->getBuilder()->where('group', $group)->where('company_id', $currentId)
        );

        return array_merge($base, $override);
    }

    public function checkIfPropertyExists(string $group, string $name): bool
    {
        if (! $this->isCompanyScoped($group)) {
            return parent::checkIfPropertyExists($group, $name);
        }

        return $this->scopedBuilder($group)->where('name', $name)->exists();
    }

    public function getPropertyPayload(string $group, string $name)
    {
        if (! $this->isCompanyScoped($group)) {
            return parent::getPropertyPayload($group, $name);
        }

        $payload = $this->preferredBuilder($group)->where('name', $name)->value('payload');

        return $this->decode((string) $payload);
    }

    public function createProperty(string $group, string $name, $payload, bool $locked = false): void
    {
        if (! $this->hasCompanyColumn()) {
            parent::createProperty($group, $name, $payload, $locked);

            return;
        }

        $this->getBuilder()->create([
            'group'      => $group,
            'name'       => $name,
            'company_id' => $this->isCompanyScoped($group) ? $this->currentCompanyId() : null,
            'payload'    => $this->encode($payload),
            'locked'     => $locked,
        ]);
    }

    public function updatePropertiesPayload(string $group, array $properties): void
    {
        $hasCompanyColumn = $this->hasCompanyColumn();

        $companyId = $hasCompanyColumn && $this->isCompanyScoped($group)
            ? $this->currentCompanyId()
            : null;

        foreach ($properties as $name => $payload) {
            $match = ['group' => $group, 'name' => $name];

            if ($hasCompanyColumn) {
                $match['company_id'] = $companyId;
            }

            $this->getBuilder()->updateOrInsert($match, ['payload' => $this->encode($payload)]);
        }
    }

    public function deleteProperty(string $group, string $name): void
    {
        if (! $this->isCompanyScoped($group)) {
            parent::deleteProperty($group, $name);

            return;
        }

        $this->scopedBuilder($group)->where('name', $name)->delete();
    }

    public function lockProperties(string $group, array $properties): void
    {
        if (! $this->isCompanyScoped($group)) {
            parent::lockProperties($group, $properties);

            return;
        }

        $this->scopedBuilder($group)->whereIn('name', $properties)->update(['locked' => true]);
    }

    public function unlockProperties(string $group, array $properties): void
    {
        if (! $this->isCompanyScoped($group)) {
            parent::unlockProperties($group, $properties);

            return;
        }

        $this->scopedBuilder($group)->whereIn('name', $properties)->update(['locked' => false]);
    }

    public function getLockedProperties(string $group): array
    {
        if (! $this->isCompanyScoped($group)) {
            return parent::getLockedProperties($group);
        }

        return $this->scopedBuilder($group)->where('locked', true)->pluck('name')->toArray();
    }

    protected function scopedBuilder(string $group)
    {
        $currentId = $this->currentCompanyId();
        $defaultId = $this->defaultCompanyId();

        return $this->getBuilder()
            ->where('group', $group)
            ->where(function ($query) use ($currentId, $defaultId) {
                $query->where('company_id', $currentId)
                    ->orWhere('company_id', $defaultId)
                    ->orWhereNull('company_id');
            });
    }

    protected function preferredBuilder(string $group)
    {
        return $this->scopedBuilder($group)
            ->orderByRaw('company_id = ? DESC', [$this->currentCompanyId()]);
    }

    protected function isCompanyScoped(string $group): bool
    {
        return $this->hasCompanyColumn() && in_array($group, self::COMPANY_SCOPED_GROUPS, true);
    }

    protected function hasCompanyColumn(): bool
    {
        if (self::$hasCompanyColumn) {
            return true;
        }

        return self::$hasCompanyColumn = Schema::hasColumn('settings', 'company_id');
    }

    protected function currentCompanyId(): ?int
    {
        $id = current_company_id();

        return $id ? (int) $id : $this->defaultCompanyId();
    }

    protected function defaultCompanyId(): ?int
    {
        $payload = $this->getBuilder()
            ->where('group', 'general')
            ->where('name', 'default_company_id')
            ->value('payload');

        $decoded = $payload !== null ? $this->decode((string) $payload) : null;

        if (is_int($decoded) && $decoded > 0) {
            return $decoded;
        }

        return DB::table('companies')->min('id');
    }
}
