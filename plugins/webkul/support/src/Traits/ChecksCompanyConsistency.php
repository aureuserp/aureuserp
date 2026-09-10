<?php

namespace Webkul\Support\Traits;

use Webkul\Support\Support\CompanyConsistencyGuard;

trait ChecksCompanyConsistency
{
    public static function bootChecksCompanyConsistency(): void
    {
        $check = function ($model) {
            if (app()->runningInConsole()) {
                return;
            }

            $model->assertCompanyConsistency();
        };

        static::creating($check);
        static::updating($check);
    }

    public function companyConsistentFields(): array
    {
        return [];
    }

    public function assertCompanyConsistency(): void
    {
        $companyId = $this->getAttribute('company_id');

        if (blank($companyId)) {
            return;
        }

        $fields = array_filter(
            $this->companyConsistentFields(),
            fn (string $field) => filled($this->getAttribute($field)) && $this->isDirty([$field, 'company_id']),
            ARRAY_FILTER_USE_KEY,
        );

        if (! $fields) {
            return;
        }

        CompanyConsistencyGuard::assert($companyId, [$this->getAttributes()], $fields);
    }
}
