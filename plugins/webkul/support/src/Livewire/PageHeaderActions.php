<?php

namespace Webkul\Support\Livewire;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Webkul\Support\Livewire\Concerns\InteractsWithPageHeaderActions;

class PageHeaderActions extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions, InteractsWithPageHeaderActions, InteractsWithSchemas;

    #[Locked]
    public string $page;

    #[Locked]
    public ?string $model = null;

    #[Locked]
    public int|string|null $recordKey = null;

    public function getPageRecord(): ?Model
    {
        if (! $this->model || $this->recordKey === null) {
            return null;
        }

        return $this->model::query()->find($this->recordKey);
    }

    public function render(): View
    {
        return view('support::livewire.page-header-actions');
    }
}
