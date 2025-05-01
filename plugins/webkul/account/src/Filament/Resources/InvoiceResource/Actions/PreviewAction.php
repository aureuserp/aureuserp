<?php

namespace Webkul\Account\Filament\Resources\InvoiceResource\Actions;

use Filament\Actions\Action;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Models\Move;
use Webkul\Support\Traits\PDFHandler;

class PreviewAction extends Action
{
    use PDFHandler;

    protected string $template = '';

    public static function getDefaultName(): ?string
    {
        return 'customers.invoice.preview';
    }

    public function getTemplate(): string
    {
        return (string) $this->template;
    }

    public function setTemplate(string $template): static
    {
        if (! view()->exists($template)) {
            throw new \InvalidArgumentException("The view [{$template}] does not exist.");
        }

        $this->template = $template;

        return $this;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('accounts::filament/resources/invoice/actions/preview.title'))
            ->color('gray')
            ->visible(fn (Move $record) => $record->state == MoveState::POSTED)
            ->icon('heroicon-o-viewfinder-circle')
            ->modalHeading(__('accounts::filament/resources/invoice/actions/preview.modal.title'))
            ->modalSubmitAction(false)
            ->modalContent(function ($record) {
                return view($this->getTemplate(), ['record' => $record]);
            });
    }
}
