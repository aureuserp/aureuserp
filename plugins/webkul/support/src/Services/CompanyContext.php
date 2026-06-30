<?php

namespace Webkul\Support\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
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

    public function allowedCompanies(): Collection
    {
        if ($this->allowed !== null) {
            return $this->allowed;
        }

        $user = auth()->user();

        if (! $user) {
            return $this->allowed = collect();
        }

        if ($this->seesAllCompanies($user)) {
            return $this->allowed = Company::query()->get();
        }

        return $this->allowed = $user->allowedCompanies()->get();
    }

    protected function seesAllCompanies($user): bool
    {
        if ($this->bypassed()) {
            return true;
        }

        return method_exists($user, 'hasRole') && $user->hasRole(['admin', 'super_admin']);
    }

    public function allowedIds(): array
    {
        return $this->allowedCompanies()->pluck('id')->all();
    }

    public function defaultId(): ?int
    {
        return auth()->user()?->default_company_id;
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
        $active = $this->activeIds();

        if (empty($active)) {
            return $this->defaultId();
        }

        $default = $this->defaultId();

        return $default && in_array($default, $active) ? $default : $active[0];
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

    public function setActive(array $ids): void
    {
        $valid = array_values(array_intersect($ids, $this->allowedIds()));

        session([self::SESSION_KEY => $valid]);
    }
}
