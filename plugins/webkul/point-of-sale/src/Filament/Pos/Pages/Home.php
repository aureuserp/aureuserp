<?php

namespace Webkul\PointOfSale\Filament\Pos\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Webkul\PointOfSale\Models\Session;
use Webkul\PointOfSale\Support\PosAccess;

class Home extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-home';

    protected static ?string $slug = '{session}/home';

    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'point-of-sale::filament.pos.pages.redirect';

    public function mount(Session $session): void
    {
        abort_unless(PosAccess::reachesActiveSession($session), 403);

        $this->redirect(Register::getUrl(['config' => $session->config_id], panel: 'pos'));
    }
}
