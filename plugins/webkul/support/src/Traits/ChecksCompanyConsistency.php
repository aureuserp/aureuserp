<?php

namespace Webkul\Support\Traits;

use Webkul\Support\Support\CompanyConsistencyGuard;

trait ChecksCompanyConsistency
{
    public static function bootChecksCompanyConsistency(): void
    {
        static::saving(function ($model) {
            // Seeders, migrations and queued workers build records in trusted, ordered
            // steps where the company is often assigned after the related keys.
            if (app()->runningInConsole()) {
                return;
            }

            $model->assertCompanyConsistency();
        });
    }

    /**
     * Map of foreign key => related model whose company must match this record's.
     *
     * @return array<string, class-string>
     */
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
