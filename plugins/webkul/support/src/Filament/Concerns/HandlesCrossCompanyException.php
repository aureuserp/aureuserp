<?php

namespace Webkul\Support\Filament\Concerns;

use Filament\Notifications\Notification;
use Webkul\Support\Exceptions\CrossCompanyException;
use Webkul\Support\Support\CompanyConsistencyGuard;

trait HandlesCrossCompanyException
{
    public function create(bool $another = false): void
    {
        try {
            $this->assertCrossCompany();

            parent::create($another);
        } catch (CrossCompanyException $exception) {
            $this->notifyCrossCompany($exception);
        }
    }

    public function save(bool $shouldRedirect = true, bool $shouldSendSavedNotification = true): void
    {
        try {
            $this->assertCrossCompany();

            parent::save($shouldRedirect, $shouldSendSavedNotification);
        } catch (CrossCompanyException $exception) {
            $this->notifyCrossCompany($exception);
        }
    }

    /**
     * Repeater (or '.' for the record itself) => [foreign key => related model].
     *
     * @return array<string, array<string, class-string>>
     */
    protected function companyConsistencyMap(): array
    {
        return [];
    }

    protected function companyConsistencyCompanyId(): ?int
    {
        return $this->data['company_id'] ?? null;
    }

    protected function assertCrossCompany(): void
    {
        $this->assertCompanyConsistency();
    }

    protected function assertCompanyConsistency(): void
    {
        $companyId = $this->companyConsistencyCompanyId();

        foreach ($this->companyConsistencyMap() as $repeater => $fields) {
            $rows = $repeater === '.'
                ? [$this->data]
                : ($this->data[$repeater] ?? null);

            if (! is_array($rows)) {
                continue;
            }

            CompanyConsistencyGuard::assert($companyId, $rows, $fields);
        }
    }

    protected function notifyCrossCompany(CrossCompanyException $exception): void
    {
        Notification::make()
            ->danger()
            ->title($exception->title())
            ->body($exception->getMessage())
            ->send();
    }
}
