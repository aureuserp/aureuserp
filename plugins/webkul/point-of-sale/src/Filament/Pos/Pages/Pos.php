<?php

namespace Webkul\PointOfSale\Filament\Pos\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Webkul\PointOfSale\Filament\Admin\Pages\Registers;

class Pos extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $slug = 'app';

    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'point-of-sale::filament.pos.pages.redirect';

    public function mount(): void
    {
        $this->redirect(Registers::getUrl(panel: 'admin'));
    }
}
