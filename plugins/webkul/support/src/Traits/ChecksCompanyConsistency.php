<?php

namespace Webkul\Support\Traits;

use Webkul\Support\Support\CompanyConsistencyGuard;

/**
 * Rejects a record that points at another company's data.
 *
 * The check lives here so every write path is covered by one rule. Presentation is
 * not this trait's job: Filament pages use HandlesCrossCompanyException and actions
 * already catch Throwable, while the API renders the exception as 422.
 *
 * Declare `use ChecksCompanyConsistency;` AFTER `use BelongsToCompany;` so that the
 * company is already resolved by the time this runs on an insert.
 *
 * Many-to-many values cannot be validated here (Eloquent fires no events for pivot
 * writes, and they are synced after the parent save) - those rely on the filtered
 * options in the form.
 */
trait ChecksCompanyConsistency
{
    public static function bootChecksCompanyConsistency(): void
    {
        $check = function ($model) {
            // Seeders, migrations and queued workers build records in trusted, ordered steps.
            if (app()->runningInConsole()) {
                return;
            }

            $model->assertCompanyConsistency();
        };

        static::creating($check);
        static::updating($check);
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
