<?php

namespace Webkul\Support\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

class CompanyContext
{
    public const SESSION_KEY = 'active_company_ids';

    protected ?Collection $allowed = null;

    protected ?Company $currentCompany = null;

    protected bool $currentCompanyResolved = false;

    public function bypassed(): bool
    {
        return Gate::allows('bypass_company_scope');
    }

    public function internalUser(): ?User
    {
        $user = auth()->user();

        return $user instanceof User ? $user : null;
    }

    public function allowedCompanies(): Collection
    {
        if ($this->allowed !== null) {
            return $this->allowed;
        }

        $user = $this->internalUser();

        if (! $user) {
            return collect();
        }

        if ($this->seesAllCompanies()) {
            return $this->allowed = Company::query()->get();
        }

        return $this->allowed = $user->allowedCompanies()->get();
    }

    public function seesAllCompanies(): bool
    {
        return $this->bypassed();
    }

    public function allowedIds(): array
    {
        return $this->allowedCompanies()->pluck('id')->all();
    }

    public function defaultId(): ?int
    {
        return $this->internalUser()?->default_company_id;
    }

    public function activeIds(): array
    {
        $allowed = $this->allowedIds();

        if (empty($allowed)) {
            return [];
        }

        $stored = session(self::SESSION_KEY);

        if (! is_array($stored) || empty($stored)) {
            $default = $this->defaultId();

            return [$default && in_array($default, $allowed) ? $default : $allowed[0]];
        }

        $active = array_values(array_intersect($stored, $allowed));

        return empty($active) ? [$allowed[0]] : $active;
    }

    public function currentId(): ?int
    {
        return $this->activeIds()[0] ?? $this->defaultId();
    }

    public function currentCompany(): ?Company
    {
        if ($this->currentCompanyResolved) {
            return $this->currentCompany;
        }

        $this->currentCompanyResolved = true;

        $id = $this->currentId();

        return $this->currentCompany = $id ? Company::find($id) : null;
    }

    public function toggle(int $id): void
    {
        $active = $this->activeIds();

        if (in_array($id, $active)) {
            if (count($active) <= 1) {
                return;
            }

            $active = array_values(array_diff($active, [$id]));
        } else {
            $active[] = $id;
        }

        $this->setActive($active);
    }

    public function setActive(array $ids, ?int $current = null): void
    {
        $valid = array_values(array_unique(array_intersect($ids, $this->allowedIds())));

        $current ??= $this->activeIds()[0] ?? null;

        if ($current && in_array($current, $valid)) {
            $valid = array_values(array_unique([$current, ...$valid]));
        }

        session([self::SESSION_KEY => $valid]);
    }
}
