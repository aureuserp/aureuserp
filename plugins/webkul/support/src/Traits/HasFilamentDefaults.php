<?php

namespace Webkul\Support\Traits;

use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;

trait HasFilamentDefaults
{
    protected function registerFilamentDefaults(): void
    {
        Fieldset::configureUsing(fn (Fieldset $fieldset) => $fieldset->columnSpanFull());

        Grid::configureUsing(fn (Grid $grid) => $grid->columnSpanFull());

        Section::configureUsing(fn (Section $section) => $section->columnSpanFull());
    }

    protected function registerHooks(): void
    {
        $version = '1.4.0';

        FilamentView::registerRenderHook(
            PanelsRenderHook::USER_MENU_PROFILE_BEFORE,
            fn (): string => Blade::render(<<<'BLADE'
                <x-filament::dropdown.list>
                    <x-filament::dropdown.list.item>
                        <div class="flex items-center gap-2">
                            <img
                                src="{{ url('cache/logo.png') }}"
                                width="24"
                                height="24"
                            />

                            {{ __('support::support.version', ['version' => $version]) }}
                        </div>
                    </x-filament::dropdown.list.item>
                </x-filament::dropdown.list>
            BLADE, [
                'version' => $version,
            ]),
        );
    }
}
