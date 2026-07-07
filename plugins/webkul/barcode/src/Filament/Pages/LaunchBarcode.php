<?php

namespace Webkul\Barcode\Filament\Pages;

use Filament\Pages\Page;
use Webkul\Support\Enums\NavigationGroup;

class LaunchBarcode extends Page
{
    protected string $view = 'barcode::filament.pages.launch-barcode';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-qr-code';

    protected static ?string $slug = 'barcode-app';

    public function mount(): void
    {
        $this->redirect(route('barcode.dashboard'), navigate: true);
    }

    public static function getNavigationLabel(): string
    {
        return __('barcode::app.filament.navigation.label');
    }

    public static function getNavigationGroup(): string | \UnitEnum
    {
        return NavigationGroup::Barcode;
    }

    public function getTitle(): string
    {
        return __('barcode::app.filament.navigation.label');
    }
}