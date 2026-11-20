<?php

namespace Webkul\Accounting\Filament\Concerns;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Callout;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Log;
use Throwable;
use Webkul\Account\Services\AccountingSetupService;

trait GatesAccountingSetup
{
    public function mount(): void
    {
        if (! $this->isAccountingSetUp()) {
            return;
        }

        parent::mount();
    }

    public function getFormActions(): array
    {
        return $this->isAccountingSetUp() ? parent::getFormActions() : [];
    }

    protected function isAccountingSetUp(): bool
    {
        $company = current_company();

        return $company !== null && app(AccountingSetupService::class)->isSetUp($company);
    }

    protected function accountingSetupCallout(Schema $schema): Schema
    {
        return $schema->components([
            Callout::make()
                ->key('accountingNotSetUp')
                ->heading(__('accounting::setup.action'))
                ->description(__('accounting::setup.notice'))
                ->icon(Heroicon::OutlinedCog6Tooth)
                ->color('warning')
                ->footerActions([$this->setupAction()]),
        ]);
    }

    protected function setupAction(): Action
    {
        return Action::make('setupAccounting')
            ->label(__('accounting::setup.action'))
            ->icon(Heroicon::OutlinedCog6Tooth)
            ->requiresConfirmation()
            ->action(function (): void {
                $this->notifyAccountingSetupResult();

                $this->redirect(static::getUrl());
            });
    }

    protected function notifyAccountingSetupResult(): void
    {
        $company = current_company();

        if ($company === null) {
            Notification::make()
                ->danger()
                ->title(__('accounting::setup.results.no-company.title'))
                ->body(__('accounting::setup.results.no-company.body'))
                ->send();

            return;
        }

        try {
            $result = app(AccountingSetupService::class)->setUp($company);
        } catch (Throwable $exception) {
            Log::error('Accounting setup failed.', [
                'company_id' => $company->id,
                'exception'  => $exception,
            ]);

            Notification::make()
                ->danger()
                ->title(__('accounting::setup.results.failed.title'))
                ->body(__('accounting::setup.results.failed.body'))
                ->send();

            return;
        }

        Notification::make()
            ->status($result->getColor())
            ->title($result->getLabel())
            ->body($result->getDescription())
            ->send();
    }
}
