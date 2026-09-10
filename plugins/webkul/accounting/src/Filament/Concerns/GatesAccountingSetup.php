<?php

namespace Webkul\Accounting\Filament\Concerns;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Callout;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
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
            Callout::make('accounting_not_set_up')
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
                app(AccountingSetupService::class)->setUp(current_company());

                Notification::make()
                    ->success()
                    ->title(__('accounting::setup.notification.title'))
                    ->body(__('accounting::setup.notification.body'))
                    ->send();

                $this->redirect(static::getUrl());
            });
    }
}
